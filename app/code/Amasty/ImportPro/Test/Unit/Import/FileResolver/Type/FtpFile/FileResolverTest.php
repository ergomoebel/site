<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */

declare(strict_types=1);

namespace Amasty\ImportPro\Test\Unit\Import\FileResolver\Type\FtpFile;

use Amasty\ImportCore\Api\Config\ProfileConfigExtension;
use Amasty\ImportCore\Import\Config\ProfileConfig;
use Amasty\ImportCore\Import\Source\MimeValidator;
use Amasty\ImportCore\Import\Utils\TmpFileManagement;
use Amasty\ImportPro\Import\FileResolver\Type\FtpFile\Config;
use Amasty\ImportCore\Import\ImportProcess;
use Amasty\ImportPro\Import\FileResolver\Type\FtpFile\FileResolver;
use Magento\Framework\Filesystem\Directory\Write;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Filesystem\Io\Ftp;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FileResolverTest extends TestCase
{
    const SOURCE_TYPE = 'csv';
    const HOST = 'example.com';
    const PATH_CSV = '/test/path/file.csv';
    const USERNAME = 'test';
    const PASSWORD = 'password';
    const IS_PASSIVE = true;
    const FILE_LIST = [['text' => 'file.csv'], ['text' => 'file.xml']];
    const FILENAME = 'test_file.csv';
    const ABSOLUTE_PATH = '/path/test_file.csv';
    const IDENTITY = 'test';

    /**
     * @var FileResolver
     */
    private $resolver;

    /**
     * @var ImportProcess|MockObject
     */
    private $importProcessMock;

    /**
     * @var Config|MockObject
     */
    private $ftpFileResolverMock;

    /**
     * @var Ftp|MockObject
     */
    private $ftpMock;

    /**
     * @var File|MockObject
     */
    private $fileMock;

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
        $this->importProcessMock->expects($this->any())
            ->method('getIdentity')
            ->willReturn(self::IDENTITY);

        $this->ftpFileResolverMock = $this->createPartialMock(
            Config::class,
            ['getPath', 'getHost', 'getUser', 'getPassword', 'isPassiveMode']
        );
        $profileConfigExtension = $this->createPartialMock(
            ProfileConfigExtension::class,
            ['getFtpFileResolver']
        );
        $profileConfigExtension->expects($this->any())
            ->method('getFtpFileResolver')
            ->willReturn($this->ftpFileResolverMock);
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

        $this->ftpMock = $this->createPartialMock(
            Ftp::class,
            ['open', 'cd', 'ls', 'read', 'close']
        );
        $this->fileMock = $this->createPartialMock(File::class, []);
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
                'ftp' => $this->ftpMock,
                'ioFile' => $this->fileMock,
                'tmpFileManagement' => $this->tmpFileManagementMock,
                'mimeValidator' => $this->mimeValidatorMock
            ]
        );
    }

    public function testExecute()
    {
        $this->setUpFtpFileResolverMock();

        $this->ftpMock->expects($this->any())
            ->method('open')->with([
            'host' => self::HOST,
            'port' => null,
            'user' => self::USERNAME,
            'password' => self::PASSWORD,
            'passive' => self::IS_PASSIVE
        ]);
        $this->ftpMock->expects($this->any())
            ->method('cd')
            ->with('/test/path');
        $this->ftpMock->expects($this->any())
            ->method('ls')
            ->willReturn(self::FILE_LIST);

        $tmpDir = $this->createPartialMock(Write::class, ['getAbsolutePath']);
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
        $this->ftpMock->expects($this->any())
            ->method('read')
            ->willReturn(true);

        $this->mimeValidatorMock->expects($this->once())
            ->method('isValid')
            ->with(self::SOURCE_TYPE, self::ABSOLUTE_PATH)
            ->willReturn(true);

        $this->assertEquals(self::ABSOLUTE_PATH, $this->resolver->execute($this->importProcessMock));
    }

    public function testExecuteNoHost()
    {
        $this->ftpFileResolverMock->expects($this->any())
            ->method('getHost')
            ->willReturn('');

        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('FTP host is empty.');
        $this->resolver->execute($this->importProcessMock);
    }

    public function testExecuteNoPath()
    {
        $this->ftpFileResolverMock->expects($this->any())
            ->method('getHost')
            ->willReturn(self::HOST);
        $this->ftpFileResolverMock->expects($this->any())
            ->method('getPath')
            ->willReturn('');

        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('File Path is empty.');
        $this->resolver->execute($this->importProcessMock);
    }

    public function testExecuteNoFile()
    {
        $this->setUpFtpFileResolverMock();

        $this->ftpMock->expects($this->any())
            ->method('ls')
            ->willReturn([]);

        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('File does not exist.');
        $this->resolver->execute($this->importProcessMock);
    }

    public function testExecuteWrongFormat()
    {
        $this->setUpFtpFileResolverMock();

        $this->ftpMock->expects($this->any())
            ->method('ls')
            ->willReturn(self::FILE_LIST);
        $this->ftpMock->expects($this->any())
            ->method('read')
            ->willReturn(true);

        $tmpDir = $this->createPartialMock(Write::class, ['getAbsolutePath', 'delete']);
        $tmpDir->expects($this->any())
            ->method('getAbsolutePath')
            ->with(self::FILENAME)
            ->willReturn(self::ABSOLUTE_PATH);
        $this->tmpFileManagementMock->expects($this->once())
            ->method('getTempDirectory')
            ->willReturn($tmpDir);
        $this->tmpFileManagementMock->expects($this->once())
            ->method('createTempFile')
            ->willReturn(self::FILENAME);
        $this->ftpMock->expects($this->once())
            ->method('read')
            ->willReturn(true);

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

    private function setUpFtpFileResolverMock()
    {
        $this->ftpFileResolverMock->expects($this->any())
            ->method('getHost')
            ->willReturn(self::HOST);
        $this->ftpFileResolverMock->expects($this->any())
            ->method('getPath')
            ->willReturn(self::PATH_CSV);
        $this->ftpFileResolverMock->expects($this->any())
            ->method('getUser')
            ->willReturn(self::USERNAME);
        $this->ftpFileResolverMock->expects($this->any())
            ->method('getPassword')
            ->willReturn(self::PASSWORD);
        $this->ftpFileResolverMock->expects($this->any())
            ->method('isPassiveMode')
            ->willReturn(self::IS_PASSIVE);
    }
}
