<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Export\FileDestination\Type\Dropbox;

use Amasty\ExportCore\Api\ExportProcessInterface;
use Amasty\ExportCore\Api\FileDestination\FileDestinationInterface;
use Amasty\ExportCore\Export\Utils\FilenameModifier;
use Amasty\ExportCore\Export\Utils\TmpFileManagement;
use Spatie\Dropbox\Client;

class FileDestination implements FileDestinationInterface
{
    /**
     * @var FilenameModifier
     */
    private $filenameModifier;

    /**
     * @var TmpFileManagement
     */
    private $tmp;

    public function __construct(
        FilenameModifier $filenameModifier,
        TmpFileManagement $tmp
    ) {
        $this->filenameModifier = $filenameModifier;
        $this->tmp = $tmp;
    }

    public function execute(ExportProcessInterface $exportProcess)
    {
        $exportProcess->addInfoMessage('Started uploading the file to Dropbox.');

        $dropboxConfig = $exportProcess->getProfileConfig()->getExtensionAttributes()->getDropboxFileDestination();
        $filename = $dropboxConfig->getFilename();
        if (!empty($filename)) {
            $filename = $this->filenameModifier->modify($filename, $exportProcess);
            $filename .= '.' . $exportProcess->getExportResult()->getExtension();
        } else {
            $filename = $exportProcess->getExportResult()->getResultFileName();
        }

        $filePath = trim((string)$dropboxConfig->getFilePath(), '/');
        if ($filePath) {
            $uploadPath = '/' . $filePath . '/';
        } else {
            $uploadPath = '/';
        }

        $tempDirectory = $this->tmp->getTempDirectory($exportProcess->getIdentity());
        $tempFileName = $this->tmp->getResultTempFileName($exportProcess->getIdentity());

        $client = new Client($dropboxConfig->getToken());
        try {
            $client->getAccountInfo(); //for access token validation
        } catch (\Exception $e) {
            throw new \RuntimeException("Wrong Access Token.");
        }

        $client->contentEndpointRequest(
            'files/upload',
            [
                'path' => $uploadPath . $filename,
                'mode' => 'add',
                'autorename' => true
            ],
            $tempDirectory->readFile($tempFileName)
        );

        $exportProcess->addInfoMessage('The file is successfully uploaded to Dropbox.');
    }
}
