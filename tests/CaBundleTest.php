<?php

namespace Composer\CaBundle;

use Psr\Log\LoggerInterface;
use Symfony\Component\Process\PhpProcess;
use PHPUnit\Framework\TestCase;

class CaBundleTest extends TestCase
{
    public function testCaPath()
    {
        $caBundle = new CaBundle();
        $this->resetEnv();
        $caPath = $caBundle::getSystemCaRootBundlePath(null);

        $this->assertTrue(file_exists($caPath));
    }

    public function testCaPathNotNull()
    {
        $caBundle = new CaBundle();
        $this->resetEnv();
        $caPathNoNull = $caBundle::getSystemCaRootBundlePath(null);

        $this->assertTrue(file_exists($caPathNoNull));
    }

    public function testCertDir()
    {
        $caBundle = new CaBundle();
        $caBundle::reset();
        $certDir = 'SSL_CERT_DIR=';
        $certPath = __DIR__.'/../res';
        $this->resetEnv();
        $this->setEnv($certDir.$certPath);
        $sslCertDir = $caBundle::getSystemCaRootBundlePath(null);

        $this->assertTrue(file_exists($sslCertDir));
    }

    public function testCertFile()
    {
        $caBundle = new CaBundle();
        $caBundle::reset();
        $certFile = 'SSL_CERT_FILE=';
        $certFilePath = __DIR__.'/../res/cacert.pem';
        $this->resetEnv();
        $this->setEnv($certFile.$certFilePath);
        $sslCertFile = $caBundle::getSystemCaRootBundlePath(null);

        $this->assertTrue(file_exists($sslCertFile));
    }

    public function testSslCaFile()
    {
        $sslCaFile = 'openssl.cafile';
        $certFilePath = __DIR__.'/../res/cacert.pem';
        ini_set($sslCaFile, $certFilePath);
        $caBundle = new CaBundle();
        $this->resetEnv();
        $openCaFile = $caBundle::getSystemCaRootBundlePath(null);

        $this->assertTrue(file_exists($openCaFile));
    }

    public function testSslCaPath()
    {
        $caBundle = new CaBundle();
        $sslCaPath = 'openssl.capath';
        $certPath = __DIR__.'/../res';
        $this->resetEnv();
        ini_set($sslCaPath, $certPath);
        $openCaPath = $caBundle::getSystemCaRootBundlePath(null);

        $this->assertTrue(file_exists($openCaPath));
    }

    public function testValidateCaFile()
    {
        $certFilePath = __DIR__.'/../res/cacert.pem';
        $caBundle = new CaBundle();
        $validResult = $caBundle::validateCaFile($certFilePath, null);

        $this->assertTrue($validResult);
    }

    public function testIsOpensslParseSafeTrue()
    {
        $stub = $this->getMock('Composer\CaBundle\CaBundleMock');
        $stub->method('isOpensslParseSafe')->willReturn(true);

        $this->assertTrue($stub->isOpensslParseSafe());
    }

    public function testIsOpensslParseSafeFalse()
    {
        $stub = $this->getMock('Composer\CaBundle\CaBundleMock');
        $stub->method('isOpensslParseSafe')->willReturn(false);

        $this->assertFalse($stub->isOpensslParseSafe());
    }

    public function setEnv($envString)
    {
        putenv($envString);
    }

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

class CaBundleMock
{
    public function isOpensslParseSafe()
    {
        $caBundle = new CaBundle();

        return $caBundle::isOpensslParseSafe();
    }
}
