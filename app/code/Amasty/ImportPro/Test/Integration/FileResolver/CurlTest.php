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
use Amasty\ImportPro\Import\FileResolver\Type\UrlFile\ConfigInterface as ResolverConfigInterface;
use Amasty\ImportPro\Import\FileResolver\Type\UrlFile\FileResolver;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;

class CurlTest extends \PHPUnit\Framework\TestCase
{
    const FILE_URL = 'https://example.net/sample_file.csv';
    const LOGIN = 'test_username';
    const PASSWORD = 'test_password';
    const FILE_CONTENTS = 'hello';

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
        /** @var FileResolver $resolver */
        $resolver = $this->objectManager->create(
            FileResolver::class,
            ['curlClient' => $this->prepareFakeCurl()]
        );

        /** @var ProfileConfig $profileConfig */
        $profileConfig = $this->objectManager->create(ProfileConfig::class);
        /** @var ResolverConfigInterface $resolverConfig */
        $resolverConfig = $this->objectManager->create(ResolverConfigInterface::class);
        $resolverConfig->setPassword(self::PASSWORD);
        $resolverConfig->setUser(self::LOGIN);
        $resolverConfig->setUrl(self::FILE_URL);
        $profileConfig->getExtensionAttributes()->setUrlFileResolver($resolverConfig);
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
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function prepareFakeCurl()
    {
        $mock = $this->createPartialMock(Curl::class, ['get', 'setCredentials', 'getStatus', 'getBody']);

        $mock->expects($this->once())->method('get')->with(self::FILE_URL);
        $mock->expects($this->once())->method('setCredentials')->with(self::LOGIN, self::PASSWORD);

        $mock->method('getBody')->willReturn(self::FILE_CONTENTS);
        $mock->method('getStatus')->willReturn(200);

        return $mock;
    }
}
