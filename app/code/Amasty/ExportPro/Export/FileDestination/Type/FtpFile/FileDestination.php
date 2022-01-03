<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


namespace Amasty\ExportPro\Export\FileDestination\Type\FtpFile;

use Amasty\ExportCore\Api\ExportProcessInterface;
use Amasty\ExportCore\Api\FileDestination\FileDestinationInterface;
use Amasty\ExportCore\Export\Utils\FilenameModifier;
use Amasty\ExportCore\Export\Utils\TmpFileManagement;
use Magento\Framework\Filesystem\Io\Ftp;
use Psr\Log\LoggerInterface;

class FileDestination implements FileDestinationInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Ftp
     */
    private $ftp;

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
        Ftp $ftp,
        FilenameModifier $filenameModifier
    ) {
        $this->logger = $logger;
        $this->ftp = $ftp;
        $this->filenameModifier = $filenameModifier;
        $this->tmp = $tmp;
    }

    public function execute(ExportProcessInterface $exportProcess)
    {
        $exportProcess->addInfoMessage('Started uploading the file to FTP.');
        /** @var ConfigInterface $ftpConfig */
        $ftpConfig = $exportProcess->getProfileConfig()->getExtensionAttributes()->getFtpFileDestination();
        if (strpos($ftpConfig->getHost(), ':') !== false) {
            [$host, $port] = explode(':', $ftpConfig->getHost(), 2);
        } else {
            $host = $ftpConfig->getHost();
            $port = 21;
        }
        $this->ftp->open(
            [
                'host'     => $host,
                'user'     => $ftpConfig->getUser(),
                'password' => $ftpConfig->getPassword(),
                'port'     => $port,
                'timeout'  => 10,
                'passive'  => $ftpConfig->isPassiveMode(),
                'path'     => $ftpConfig->getPath()
            ]
        );

        $filename = $ftpConfig->getFilename();
        if (!empty($filename)) {
            $filename = $this->filenameModifier->modify($filename, $exportProcess);
            $filename .= '.' . $exportProcess->getExportResult()->getExtension();
        } else {
            $filename = $exportProcess->getExportResult()->getResultFileName();
        }

        $tempDirectory = $this->tmp->getTempDirectory($exportProcess->getIdentity());
        $tempFileName = $this->tmp->getResultTempFileName($exportProcess->getIdentity());
        $absoluteTmpPath = $tempDirectory->getAbsolutePath($tempFileName);
        if (!$this->ftp->write($filename, $absoluteTmpPath)) {
            $lastError = error_get_last();
            if (!empty($lastError['message'])) {
                throw new \RuntimeException($lastError['message']);
            }
        }
        $this->ftp->close();
        $exportProcess->addInfoMessage('The file is successfully uploaded to FTP.');
    }
}
