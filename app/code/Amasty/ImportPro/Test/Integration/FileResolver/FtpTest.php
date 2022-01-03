<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */


declare(strict_types=1);

namespace Amasty\ImportPro\Test\Integration\FileResolver;

use Amasty\ImportCore\Api\ImportProcessInterface;
use Amasty\ImportCore\Import\Config\ProfileConfig;
use Amasty\ImportPro\Import\FileResolver\Type\FtpFile\FileResolver;
use Amasty\ImportPro\Import\FileResolver\Type\FtpFile\ConfigInterface as ResolverConfigInterface;
use Amasty\ImportPro\Test\Integration\FileResolver\Ftp\FakeFtp;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

class FtpTest extends \PHPUnit\Framework\TestCase
{
    const HOST = 'example.net';
    const PORT = 1488;
    const LOGIN = 'test_username';
    const PASSWORD = 'test_password';
    const FILE_CONTENTS = 'hello';
    const PATH_TO_FILE = '/path/to/file.csv';

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }

    public function testExecute()
    {
        /** @var FakeFtp $fakeFtp */
        $fakeFtp = $this->objectManager->create(FakeFtp::class, [
            'host'     => self::HOST,
            'port'     => self::PORT,
            'user'     => self::LOGIN,
            'password' => self::PASSWORD,
            'files'    => [
                self::PATH_TO_FILE => self::FILE_CONTENTS
            ]
        ]);

        /** @var FileResolver $resolver */
        $resolver = $this->objectManager->create(
            FileResolver::class,
            ['ftp' => $fakeFtp]
        );

        /** @var ProfileConfig $profileConfig */
        $profileConfig = $this->objectManager->create(ProfileConfig::class);
        /** @var ResolverConfigInterface $resolverConfig */
        $resolverConfig = $this->objectManager->create(ResolverConfigInterface::class);
        $resolverConfig->setPassword(self::PASSWORD);
        $resolverConfig->setUser(self::LOGIN);
        $resolverConfig->setHost(self::HOST . ':' . self::PORT);
        $resolverConfig->setPath(self::PATH_TO_FILE);
        $resolverConfig->setIsPassiveMode(true);
        $profileConfig->getExtensionAttributes()->setFtpFileResolver($resolverConfig);
        $profileConfig->setSourceType('csv');

        /** @var ImportProcessInterface $importProcess */
        $importProcess = $this->objectManager->create(
            ImportProcessInterface::class,
            [
                'identity'      => 'some_identity',
                'profileConfig' => $profileConfig
            ]
        );

        $filePath = $resolver->execute($importProcess);

        $this->assertFileExists($filePath);
        $this->assertEquals(self::FILE_CONTENTS, file_get_contents($filePath));
        $this->assertFalse($fakeFtp->isOpened());
    }
}
