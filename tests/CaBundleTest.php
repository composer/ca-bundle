<?php

namespace Composer\CaBundle;

use Psr\Log\LoggerInterface;
use Symfony\Component\Process\PhpProcess;
use PHPUnit\Framework\TestCase;

class CaBundleTest extends TestCase
{
    public function testCaPath(): void
    {
        $caBundle = new CaBundle();
        $this->resetEnv();
        $caPath = $caBundle::getSystemCaRootBundlePath(null);

        $this->assertFileExists($caPath);
    }

    public function testFindDistrustedCertificate(): void
    {
        $ev = (string) file_get_contents(__DIR__.'/Fixtures/distrusted/em-ev-tls-ca-g3b.pem');
        $ov = (string) file_get_contents(__DIR__.'/Fixtures/distrusted/em-ov-tls-ca-g3b.pem');

        $this->assertSame('EM EV TLS CA - G3B', CaBundle::findDistrustedCertificate(array($ev)));
        $this->assertSame('EM OV TLS CA - G3B', CaBundle::findDistrustedCertificate(array('garbage', $ov, $ev)));
        $this->assertSame('EM EV TLS CA - G3B', CaBundle::findDistrustedCertificate(array(openssl_x509_read($ev))));
    }

    public function testFindDistrustedCertificateIgnoresTrustedCertificates(): void
    {
        preg_match_all('{-----BEGIN CERTIFICATE-----.+?-----END CERTIFICATE-----}s', (string) file_get_contents(CaBundle::getBundledCaBundlePath()), $match);

        $this->assertGreaterThan(100, count($match[0]));
        $this->assertNull(CaBundle::findDistrustedCertificate($match[0]));
        $this->assertNull(CaBundle::findDistrustedCertificate(array()));
    }

    public function testCaPathNotNull(): void
    {
        $caBundle = new CaBundle();
        $this->resetEnv();
        $caPathNoNull = $caBundle::getSystemCaRootBundlePath(null);

        $this->assertFileExists($caPathNoNull);
    }

    public function testCertDir(): void
    {
        $caBundle = new CaBundle();
        $caBundle::reset();
        $certDir = 'SSL_CERT_DIR=';
        $certPath = __DIR__.'/../res';
        $this->resetEnv();
        $this->setEnv($certDir.$certPath);
        $sslCertDir = $caBundle::getSystemCaRootBundlePath(null);

        $this->assertFileExists($sslCertDir);
    }

    public function testCertFile(): void
    {
        $caBundle = new CaBundle();
        $caBundle::reset();
        $certFile = 'SSL_CERT_FILE=';
        $certFilePath = __DIR__.'/../res/cacert.pem';
        $this->resetEnv();
        $this->setEnv($certFile.$certFilePath);
        $sslCertFile = $caBundle::getSystemCaRootBundlePath(null);

        $this->assertFileExists($sslCertFile);
    }

    public function testSslCaFile(): void
    {
        $sslCaFile = 'openssl.cafile';
        $certFilePath = __DIR__.'/../res/cacert.pem';
        ini_set($sslCaFile, $certFilePath);
        $caBundle = new CaBundle();
        $this->resetEnv();
        $openCaFile = $caBundle::getSystemCaRootBundlePath(null);

        $this->assertFileExists($openCaFile);
    }

    public function testSslCaPath(): void
    {
        $caBundle = new CaBundle();
        $sslCaPath = 'openssl.capath';
        $certPath = __DIR__.'/../res';
        $this->resetEnv();
        ini_set($sslCaPath, $certPath);
        $openCaPath = $caBundle::getSystemCaRootBundlePath(null);

        $this->assertFileExists($openCaPath);
    }

    public function testValidateCaFile(): void
    {
        $certFilePath = __DIR__.'/../res/cacert.pem';
        $caBundle = new CaBundle();
        $validResult = $caBundle::validateCaFile($certFilePath, null);

        $this->assertTrue($validResult);
    }

    public function testValidateTrustedCaFile(): void
    {
        $certFilePath = __DIR__.'/Fixtures/ca-bundle.trust.crt';
        $caBundle = new CaBundle();
        $validResult = $caBundle::validateCaFile($certFilePath, null);

        $this->assertTrue($validResult);
    }

    public function testOpenBaseDir(): void
    {
        $oldValue = ini_get('open_basedir') ?: '';
        ini_set('open_basedir', dirname(__DIR__));
        $certFilePath = CaBundle::getSystemCaRootBundlePath();
        $validResult = CaBundle::validateCaFile($certFilePath, null);
        $this->assertTrue($validResult);
        ini_set('open_basedir', $oldValue);
    }

    public function setEnv(string $envString): void
    {
        putenv($envString);
    }

    public function resetEnv(): void
    {
        $certDir = 'SSL_CERT_DIR=';
        $certFile = 'SSL_CERT_FILE=';
        $sslCaFile = 'openssl.cafile';
        $sslCaPath = 'openssl.capath';

        $this->setEnv($certDir);
        $this->setEnv($certFile);
    }
}
