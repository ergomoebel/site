<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */

declare(strict_types=1);

namespace Amasty\ImportPro\Import\FileResolver\Type\SftpFile;

use Amasty\ImportCore\Api\FileResolver\FileResolverInterface;
use Amasty\ImportCore\Api\ImportProcessInterface;
use Amasty\ImportCore\Import\Source\MimeValidator;
use Amasty\ImportCore\Import\Utils\TmpFileManagement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Filesystem\Io\Sftp;

class FileResolver implements FileResolverInterface
{
    /**
     * @var Sftp
     */
    private $sftp;

    /**
     * @var File
     */
    private $ioFile;

    /**
     * @var MimeValidator
     */
    private $mimeValidator;

    /**
     * @var TmpFileManagement
     */
    private $tmpFileManagement;

    public function __construct(
        Sftp $sftp,
        File $ioFile,
        MimeValidator $mimeValidator,
        TmpFileManagement $tmpFileManagement
    ) {
        $this->sftp = $sftp;
        $this->ioFile = $ioFile;
        $this->mimeValidator = $mimeValidator;
        $this->tmpFileManagement = $tmpFileManagement;
    }

    public function execute(ImportProcessInterface $importProcess): string
    {
        $config = $importProcess->getProfileConfig()->getExtensionAttributes()->getSftpFileResolver();
        $host = $config->getHost();
        if (empty($host)) {
            throw new \RuntimeException('SFTP host is empty.');
        }
        $fileName = $config->getPath();
        if (empty($fileName)) {
            throw new \RuntimeException('File Path is empty.');
        }
        $pathInfo = $this->ioFile->getPathInfo($fileName);

        try {
            $this->sftp->open(
                [
                    'host'     => $host,
                    'username' => $config->getUser(),
                    'password' => $config->getPassword()
                ]
            );
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }

        $this->sftp->cd($pathInfo['dirname']);
        $fileList = $this->sftp->ls();
        if (!in_array($pathInfo['basename'], array_column($fileList, 'text'))) {
            throw new \RuntimeException('File does not exist.');
        }

        $tmpDir = $this->tmpFileManagement->getTempDirectory($importProcess->getIdentity());
        $fileName = $this->tmpFileManagement->createTempFile($tmpDir);

        if (!$this->sftp->read($pathInfo['basename'], $tmpDir->getAbsolutePath($fileName))) {
            try {
                $tmpDir->delete($fileName);
            } catch (LocalizedException $e) {
                null;
            }
            $this->sftp->close();
            throw new \RuntimeException('SFTP File download. Something went wrong.');
        }

        $this->sftp->close();

        $filePath = $tmpDir->getAbsolutePath($fileName);
        $sourceType = $importProcess->getProfileConfig()->getSourceType();
        if (!$this->mimeValidator->isValid($sourceType, $filePath)) {
            $tmpDir->delete($fileName);
            throw new \RuntimeException('The import file doesn\'t match the selected format.');
        }

        return $filePath;
    }
}
