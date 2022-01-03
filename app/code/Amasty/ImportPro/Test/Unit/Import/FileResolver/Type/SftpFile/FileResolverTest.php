<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */

declare(strict_types=1);

namespace Amasty\ImportPro\Test\Unit\Import\FileResolver\Type\SftpFile;

use Amasty\ImportCore\Api\Config\ProfileConfigExtension;
use Amasty\ImportCore\Import\Config\ProfileConfig;
use Amasty\ImportCore\Import\Source\MimeValidator;
use Amasty\ImportCore\Import\Utils\TmpFileManagement;
use Amasty\ImportPro\Import\FileResolver\Type\SftpFile\Config;
use Amasty\ImportCore\Import\ImportProcess;
use Amasty\ImportPro\Import\FileResolver\Type\SftpFile\FileResolver;
use Magento\Framework\Filesystem\Directory\Write;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Filesystem\Io\Sftp;
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
    private $sftpFileResolverMock;

    /**
     * @var Sftp|MockObject
     */
    private $sftpMock;

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

        $this->sftpFileResolverMock = $this->createPartialMock(
            Config::class,
            ['getPath', 'getHost', 'getUser', 'getPassword']
        );
        $profileConfigExtension = $this->createPartialMock(
            ProfileConfigExtension::class,
            ['getSftpFileResolver']
        );
        $profileConfigExtension->expects($this->any())
            ->method('getSftpFileResolver')
            ->willReturn($this->sftpFileResolverMock);
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

        $this->sftpMock = $this->createPartialMock(
            Sftp::class,
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
                'sftp' => $this->sftpMock,
                'ioFile' => $this->fileMock,
                'tmpFileManagement' => $this->tmpFileManagementMock,
                'mimeValidator' => $this->mimeValidatorMock
            ]
        );
    }

    public function testExecute()
    {
        $this->setUpSftpFileResolverMock();

        $this->sftpMock->expects($this->any())
            ->method('open')->with([
            'host' => self::HOST,
            'username' => self::USERNAME,
            'password' => self::PASSWORD,
        ]);
        $this->sftpMock->expects($this->any())
            ->method('cd')
            ->with('/test/path');
        $this->sftpMock->expects($this->any())
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
        $this->sftpMock->expects($this->any())
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
        $this->sftpFileResolverMock->expects($this->any())
            ->method('getHost')
            ->willReturn('');

        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('SFTP host is empty.');
        $this->resolver->execute($this->importProcessMock);
    }

    public function testExecuteNoPath()
    {
        $this->sftpFileResolverMock->expects($this->any())
            ->method('getHost')
            ->willReturn(self::HOST);
        $this->sftpFileResolverMock->expects($this->any())
            ->method('getPath')
            ->willReturn('');

        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('File Path is empty.');
        $this->resolver->execute($this->importProcessMock);
    }

    public function testExecuteNoFile()
    {
        $this->setUpSftpFileResolverMock();

        $this->sftpMock->expects($this->any())
            ->method('ls')
            ->willReturn([]);

        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('File does not exist.');
        $this->resolver->execute($this->importProcessMock);
    }

    public function testExecuteWrongFormat()
    {
        $this->setUpSftpFileResolverMock();

        $this->sftpMock->expects($this->any())
            ->method('ls')
            ->willReturn(self::FILE_LIST);
        $this->sftpMock->expects($this->any())
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
        $this->sftpMock->expects($this->once())
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

    private function setUpSftpFileResolverMock()
    {
        $this->sftpFileResolverMock->expects($this->any())
            ->method('getHost')
            ->willReturn(self::HOST);
        $this->sftpFileResolverMock->expects($this->any())
            ->method('getPath')
            ->willReturn(self::PATH_CSV);
        $this->sftpFileResolverMock->expects($this->any())
            ->method('getUser')
            ->willReturn(self::USERNAME);
        $this->sftpFileResolverMock->expects($this->any())
            ->method('getPassword')
            ->willReturn(self::PASSWORD);
    }
}
