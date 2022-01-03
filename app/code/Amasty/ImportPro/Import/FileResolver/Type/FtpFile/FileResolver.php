<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */

declare(strict_types=1);

namespace Amasty\ImportPro\Import\FileResolver\Type\FtpFile;

use Amasty\ImportCore\Api\FileResolver\FileResolverInterface;
use Amasty\ImportCore\Api\ImportProcessInterface;
use Amasty\ImportCore\Import\Source\MimeValidator;
use Amasty\ImportCore\Import\Utils\TmpFileManagement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Filesystem\Io\Ftp;

class FileResolver implements FileResolverInterface
{
    /**
     * @var Ftp
     */
    private $ftp;

    /**
     * @var File
     */
    private $ioFile;

    /**
     * @var TmpFileManagement
     */
    private $tmpFileManagement;

    /**
     * @var MimeValidator
     */
    private $mimeValidator;

    public function __construct(
        Ftp $ftp,
        File $ioFile,
        TmpFileManagement $tmpFileManagement,
        MimeValidator $mimeValidator
    ) {
        $this->ftp = $ftp;
        $this->ioFile = $ioFile;
        $this->tmpFileManagement = $tmpFileManagement;
        $this->mimeValidator = $mimeValidator;
    }

    public function execute(ImportProcessInterface $importProcess): string
    {
        $config = $importProcess->getProfileConfig()->getExtensionAttributes()->getFtpFileResolver();

        $host = $config->getHost();
        $port = null;
        if (empty($host)) {
            throw new \RuntimeException('FTP host is empty.');
        }
        //phpcs:ignore
        $parsedHost = \parse_url($host);
        if (!empty($parsedHost['port'])) {
            $host = $parsedHost['host'];
            $port = $parsedHost['port'];
        }
        $fileName = $config->getPath();
        if (empty($fileName)) {
            throw new \RuntimeException('File Path is empty.');
        }
        $pathInfo = $this->ioFile->getPathInfo($fileName);

        try {
            $this->ftp->open(
                [
                    'host'     => $host,
                    'port'     => $port,
                    'user'     => $config->getUser(),
                    'password' => $config->getPassword(),
                    'passive'  => $config->isPassiveMode()
                ]
            );
        } catch (LocalizedException $e) {
            throw new \RuntimeException($e->getMessage());
        }
        $this->ftp->cd($pathInfo['dirname']);
        $fileList = $this->ftp->ls();
        if (!in_array($pathInfo['basename'], array_column($fileList, 'text'))) {
            throw new \RuntimeException('File does not exist.');
        }

        $tmpDir = $this->tmpFileManagement->getTempDirectory($importProcess->getIdentity());
        $fileName = $this->tmpFileManagement->createTempFile($tmpDir);

        if (!$this->ftp->read($pathInfo['basename'], $tmpDir->getAbsolutePath($fileName))) {
            try {
                $tmpDir->delete($fileName);
            } catch (LocalizedException $e) {
                null;
            }
            $this->ftp->close();
            throw new \RuntimeException('FTP File download. Something went wrong');
        }

        $this->ftp->close();

        $filePath = $tmpDir->getAbsolutePath($fileName);
        $sourceType = $importProcess->getProfileConfig()->getSourceType();
        if (!$this->mimeValidator->isValid($sourceType, $filePath)) {
            $tmpDir->delete($fileName);
            throw new \RuntimeException('The import file doesn\'t match the selected format.');
        }

        return $tmpDir->getAbsolutePath($fileName);
    }
}
