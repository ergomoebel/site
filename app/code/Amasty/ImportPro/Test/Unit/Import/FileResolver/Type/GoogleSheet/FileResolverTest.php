<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */

declare(strict_types=1);

namespace Amasty\ImportPro\Test\Unit\Import\FileResolver\Type\GoogleSheet;

use Amasty\ImportCore\Api\Config\ProfileConfigExtension;
use Amasty\ImportCore\Import\Config\ProfileConfig;
use Amasty\ImportCore\Import\ImportProcess;
use Amasty\ImportCore\Import\Source\MimeValidator;
use Amasty\ImportCore\Import\Utils\TmpFileManagement;
use Amasty\ImportPro\Import\FileResolver\Type\GoogleSheet\Config;
use Amasty\ImportPro\Import\FileResolver\Type\GoogleSheet\FileResolver;
use Magento\Framework\Filesystem\Directory\Write;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FileResolverTest extends TestCase
{
    const SOURCE_TYPE = 'csv';
    const URL = 'test.example.com/sheets/edit';
    const FILE_CONTENT = 'sample content';
    const IDENTITY = 'test';
    const FILENAME = 'test_file.csv';
    const ABSOLUTE_PATH = '/path/test_file.csv';

    /**
     * @var FileResolver
     */
    private $resolver;

    /**
     * @var Config|MockObject
     */
    private $googleSheetFileResolverMock;

    /**
     * @var ImportProcess|MockObject
     */
    private $importProcessMock;

    /**
     * @var Curl|MockObject
     */
    private $curlClientMock;

    /**
     * @var TmpFileManagement|MockObject
     */
    private $tmpFileManagementMock;

    /**
     * @var MimeValidator|MockObject
     */
    private $mimeValidatorMock;

    public function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->importProcessMock = $this->createPartialMock(
            ImportProcess::class,
            ['getProfileConfig', 'getIdentity']
        );

        $this->googleSheetFileResolverMock = $this->createPartialMock(
            Config::class,
            ['getUrl']
        );
        $profileConfigExtension = $this->createPartialMock(
            ProfileConfigExtension::class,
            ['getGoogleSheetFileResolver']
        );
        $profileConfigExtension->expects($this->any())
            ->method('getGoogleSheetFileResolver')
            ->willReturn($this->googleSheetFileResolverMock);
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

        $this->curlClientMock = $this->createPartialMock(
            Curl::class,
            ['setOption', 'get', 'getStatus', 'getBody']
        );
        $this->tmpFileManagementMock = $this->createPartialMock(
            TmpFileManagement::class,
            ['getTempDirectory', 'createTempFile']
        );
        $this->mimeValidatorMock = $this->createPartialMock(
            MimeValidator::class,
            ['isValid']
        );
        $this->resolver = $objectManager->getObject(
            FileResolver::class,
            [
                'curlClient' => $this->curlClientMock,
                'tmpFileManagement' => $this->tmpFileManagementMock,
                'mimeValidator' => $this->mimeValidatorMock
            ]
        );
    }

    public function testExecuteNoUrl()
    {
        $this->googleSheetFileResolverMock->expects($this->any())
            ->method('getUrl')
            ->willReturn('');

        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('Google Sheet Url couldn\'t be empty.');
        $this->resolver->execute($this->importProcessMock);
    }

    public function testExecuteNoFile()
    {
        $this->googleSheetFileResolverMock->expects($this->any())
            ->method('getUrl')
            ->willReturn(self::URL);
        $this->curlClientMock->expects($this->any())
            ->method('getStatus')
            ->willReturn(404);

        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('File Not Found.');
        $this->resolver->execute($this->importProcessMock);
    }

    public function testExecuteWrongFormat()
    {
        $this->googleSheetFileResolverMock->expects($this->any())
            ->method('getUrl')
            ->willReturn(self::URL);

        $this->curlClientMock->expects($this->any())
            ->method('getStatus')
            ->willReturn(200);
        $this->curlClientMock->expects($this->any())
            ->method('getBody')
            ->willReturn(self::FILE_CONTENT);

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
}
