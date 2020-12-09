<?php

namespace Composer\CaBundle;

use Psr\Log\LoggerInterface;
use Symfony\Component\Process\PhpProcess;
use PHPUnit\Framework\TestCase;

class CaBundleTest extends TestCase
{
    /**
     * @return void
     */
    public function testCaPath()
    {
        $caBundle = new CaBundle();
        $this->resetEnv();
        $caPath = $caBundle::getSystemCaRootBundlePath(null);

        $this->assertFileExists($caPath);
    }

    /**
     * @return void
     */
    public function testCaPathNotNull()
    {
        $caBundle = new CaBundle();
        $this->resetEnv();
        $caPathNoNull = $caBundle::getSystemCaRootBundlePath(null);

        $this->assertFileExists($caPathNoNull);
    }

    /**
     * @return void
     */
    public function testCertDir()
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

    /**
     * @return void
     */
    public function testCertFile()
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

    /**
     * @return void
     */
    public function testSslCaFile()
    {
        $sslCaFile = 'openssl.cafile';
        $certFilePath = __DIR__.'/../res/cacert.pem';
        ini_set($sslCaFile, $certFilePath);
        $caBundle = new CaBundle();
        $this->resetEnv();
        $openCaFile = $caBundle::getSystemCaRootBundlePath(null);

        $this->assertFileExists($openCaFile);
    }

    /**
     * @return void
     */
    public function testSslCaPath()
    {
        $caBundle = new CaBundle();
        $sslCaPath = 'openssl.capath';
        $certPath = __DIR__.'/../res';
        $this->resetEnv();
        ini_set($sslCaPath, $certPath);
        $openCaPath = $caBundle::getSystemCaRootBundlePath(null);

        $this->assertFileExists($openCaPath);
    }

    /**
     * @return void
     */
    public function testValidateCaFile()
    {
        $certFilePath = __DIR__.'/../res/cacert.pem';
        $caBundle = new CaBundle();
        $validResult = $caBundle::validateCaFile($certFilePath, null);

        $this->assertTrue($validResult);
    }

    /**
     * @return void
     */
    public function testValidateTrustedCaFile()
    {
        $certFilePath = __DIR__.'/Fixtures/ca-bundle.trust.crt';
        $caBundle = new CaBundle();
        $validResult = $caBundle::validateCaFile($certFilePath, null);

        $this->assertTrue($validResult);
    }

    /**
     * @return void
     */
    public function testOpenBaseDir()
    {
        $oldValue = ini_get('open_basedir') ?: '';
        ini_set('open_basedir', dirname(__DIR__));
        $certFilePath = CaBundle::getSystemCaRootBundlePath();
        $validResult = CaBundle::validateCaFile($certFilePath, null);
        $this->assertTrue($validResult);
        ini_set('open_basedir', $oldValue);
    }

    /**
     * @param string $envString
     * @return void
     */
    public function setEnv($envString)
    {
        putenv($envString);
    }

    /**
     * @return void
     */
    public function resetEnv()
    {
        $certDir = 'SSL_CERT_DIR=';
        $certFile = 'SSL_CERT_FILE=';
        $sslCaFile = 'openssl.cafile';
        $sslCaPath = 'openssl.capath';

        $this->setEnv($certDir);
        $this->setEnv($certFile);
    }
}
