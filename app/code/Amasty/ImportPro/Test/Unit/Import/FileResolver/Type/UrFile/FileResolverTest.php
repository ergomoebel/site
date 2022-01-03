<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */

declare(strict_types=1);

namespace Amasty\ImportPro\Test\Unit\Import\FileResolver\Type\UrFile;

use Amasty\ImportCore\Api\Config\ProfileConfigExtension;
use Amasty\ImportCore\Import\Config\ProfileConfig;
use Amasty\ImportCore\Import\ImportProcess;
use Amasty\ImportCore\Import\Source\MimeValidator;
use Amasty\ImportCore\Import\Utils\TmpFileManagement;
use Amasty\ImportPro\Import\FileResolver\Type\UrlFile\FileResolver;
use Amasty\ImportPro\Import\FileResolver\Type\UrlFile\Config;
use Magento\Framework\Filesystem\Directory\Write;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FileResolverTest extends TestCase
{
    const SOURCE_TYPE = 'csv';
    const URL = 'example.com';
    const USER = 'test';
    const PASSWORD = 'password';
    const CONTENT = 'sample,content';
    const IDENTITY = 'test';
    const FILENAME = 'test_file.csv';
    const ABSOLUTE_PATH = '/path/test_file.csv';

    /**
     * @var FileResolver
     */
    private $resolver;

    /**
     * @var ImportProcess|MockObject
     */
    private $importProcessMock;

    /**
     * @var Curl|MockObject
     */
    private $curlMock;

    /**
     * @var MimeValidator|MockObject
     */
    private $mimeValidatorMock;

    /**
     * @var TmpFileManagement|MockObject
     */
    private $tmpFileManagementMock;

    /**
     * @var Config|MockObject
     */
    private $urlFileResolverMock;

    public function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->importProcessMock = $this->createPartialMock(
            ImportProcess::class,
            ['getProfileConfig', 'getIdentity']
        );

        $this->urlFileResolverMock = $this->createPartialMock(
            Config::class,
            ['getUrl', 'getUser', 'getPassword']
        );
        $profileConfigExtension = $this->createPartialMock(
            ProfileConfigExtension::class,
            ['getUrlFileResolver']
        );
        $profileConfigExtension->expects($this->any())
            ->method('getUrlFileResolver')
            ->willReturn($this->urlFileResolverMock);
        $profileConfig = $this->createPartialMock(
            ProfileConfig::class,
            ['getExtensionAttributes', 'getSourceType']
        );
        $profileConfig->expects($this->any())
            ->method('getExtensionAttributes')
            ->willReturn($profileConfigExtension);
        $profileConfig->expects($this->any())
            ->method('getSourceType')
            ->willReturn(self::SOURCE_TYPE);
        $this->importProcessMock->expects($this->any())
            ->method('getProfileConfig')
            ->willReturn($profileConfig);

        $this->curlMock = $this->createPartialMock(
            Curl::class,
            ['getStatus', 'get', 'setCredentials', 'getBody']
        );
        $this->mimeValidatorMock = $this->createPartialMock(
            MimeValidator::class,
            ['isValid']
        );
        $this->tmpFileManagementMock = $this->createPartialMock(
            TmpFileManagement::class,
            ['getTempDirectory', 'createTempFile']
        );
        $this->resolver = $objectManager->getObject(
            FileResolver::class,
            [
                'curlClient' => $this->curlMock,
                'mimeValidator' => $this->mimeValidatorMock,
                'tmpFileManagement' => $this->tmpFileManagementMock
            ]
        );
    }

    public function testExecuteNoUrl()
    {
        $this->urlFileResolverMock->expects($this->any())
            ->method('getUrl')
            ->willReturn('');

        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('Url couldn\'t be empty.');
        $this->resolver->execute($this->importProcessMock);
    }

    /**
     * @dataProvider executeCurlErrorsDataProvider
     */
    public function testExecuteCurlErrors($curlStatus, $errorMessage)
    {
        $this->urlFileResolverMock->expects($this->any())
            ->method('getUrl')
            ->willReturn(self::URL);
        $this->curlMock->expects($this->any())
            ->method('getStatus')
            ->willReturn($curlStatus);

        $this->expectException('RuntimeException');
        $this->expectExceptionMessage($errorMessage);
        $this->resolver->execute($this->importProcessMock);
    }

    public function testExecuteWrongFormat()
    {
        $this->urlFileResolverMock->expects($this->any())
            ->method('getUrl')
            ->willReturn(self::URL);
        $this->curlMock->expects($this->any())
            ->method('getStatus')
            ->willReturn(200);
        $this->curlMock->expects($this->any())
            ->method('getBody')
            ->willReturn(self::CONTENT);

        $this->importProcessMock->expects($this->any())
            ->method('getIdentity')
            ->willReturn(self::IDENTITY);
        $tmpDir = $this->createPartialMock(
            Write::class,
            ['getAbsolutePath', 'writeFile', 'delete']
        );
        $tmpDir->expects($this->any())
            ->method('getAbsolutePath')
            ->with(self::FILENAME)
            ->willReturn(self::ABSOLUTE_PATH);
        $this->tmpFileManagementMock->expects($this->any())
            ->method('getTempDirectory')
            ->willReturn($tmpDir);
        $this->tmpFileManagementMock->expects($this->any())
            ->method('createTempFile')
            ->willReturn(self::FILENAME);

        $this->mimeValidatorMock->expects($this->once())
            ->method('isValid')
            ->with(self::SOURCE_TYPE, self::ABSOLUTE_PATH)
            ->willReturn(false);
        $tmpDir->expects($this->once())
            ->method('delete')
            ->with(self::FILENAME);

        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('The import file doesn\'t match the selected format.');
        $this->resolver->execute($this->importProcessMock);
    }

    public function executeCurlErrorsDataProvider()
    {
        return [
            [401, 'Basic Auth. Credentials Required.'],
            [404, 'File Not Found.'],
            [502, 'Error occurred while downloading the file. Error code: 502']
        ];
    }
}
