<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


namespace Amasty\ExportPro\Export\FileDestination\Type\SftpFile;

use Amasty\ExportCore\Api\ExportProcessInterface;
use Amasty\ExportCore\Api\FileDestination\FileDestinationInterface;
use Amasty\ExportCore\Export\Utils\FilenameModifier;
use Amasty\ExportCore\Export\Utils\TmpFileManagement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Io\Sftp;
use Psr\Log\LoggerInterface;

class FileDestination implements FileDestinationInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Sftp
     */
    private $sftp;

    /**
     * @var FilenameModifier
     */
    private $filenameModifier;

    /**
     * @var TmpFileManagement
     */
    private $tmp;

    public function __construct(
        TmpFileManagement $tmp,
        LoggerInterface $logger,
        Sftp $sftp,
        FilenameModifier $filenameModifier
    ) {
        $this->logger = $logger;
        $this->sftp = $sftp;
        $this->filenameModifier = $filenameModifier;
        $this->tmp = $tmp;
    }

    public function execute(ExportProcessInterface $exportProcess)
    {
        $exportProcess->addInfoMessage('Started uploading the file to SFTP.');
        /** @var ConfigInterface $sftpConfig */
        $sftpConfig = $exportProcess->getProfileConfig()->getExtensionAttributes()->getSftpFileDestination();
        $this->sftp->open(
            [
                'host'     => $sftpConfig->getHost(),
                'username' => $sftpConfig->getUser(),
                'password' => $sftpConfig->getPassword(),
                'timeout'  => 10
            ]
        );
        $path = $this->sftp->cd($sftpConfig->getPath());
        if (!$path) {
            $this->sftp->close();
            throw new LocalizedException(__('The path is invalid. Verify and try again.'));
        }
        $filename = $sftpConfig->getFilename();
        if (!empty($filename)) {
            $filename = $this->filenameModifier->modify($filename, $exportProcess);
            $filename .= '.' . $exportProcess->getExportResult()->getExtension();
        } else {
            $filename = $exportProcess->getExportResult()->getResultFileName();
        }

        $tempDirectory = $this->tmp->getTempDirectory($exportProcess->getIdentity());
        $tempFileName = $this->tmp->getResultTempFileName($exportProcess->getIdentity());
        $absoluteTmpPath = $tempDirectory->getAbsolutePath($tempFileName);
        if (!$this->sftp->write($filename, $absoluteTmpPath)) {
            $lastError = error_get_last();
            if (!empty($lastError['message'])) {
                throw new \RuntimeException($lastError['message']);
            }
        }
        $exportProcess->addInfoMessage('The file is successfully uploaded to SFTP.');
    }
}
