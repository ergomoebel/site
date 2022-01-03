<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


namespace Amasty\ExportPro\Export\FileDestination\Type\GoogleDrive;

use Amasty\ExportCore\Api\ExportProcessInterface;
use Amasty\ExportCore\Api\FileDestination\FileDestinationInterface;
use Amasty\ExportCore\Export\Utils\FilenameModifier;
use Amasty\ExportCore\Export\Utils\TmpFileManagement;
use Amasty\ExportPro\Export\FileDestination\Type\GoogleDrive\Utils\KeyFileUploader;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\File\Mime;

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

    /**
     * @var KeyFileUploader
     */
    private $keyFileUploader;

    /**
     * @var Mime
     */
    private $mime;

    public function __construct(
        TmpFileManagement $tmp,
        FilenameModifier $filenameModifier,
        KeyFileUploader $keyFileUploader,
        Mime $mime
    ) {
        $this->filenameModifier = $filenameModifier;
        $this->tmp = $tmp;
        $this->keyFileUploader = $keyFileUploader;
        $this->mime = $mime;
    }

    public function execute(ExportProcessInterface $exportProcess)
    {
        $exportProcess->addInfoMessage('Started uploading the file to Google Drive.');

        $driveConfig = $exportProcess->getProfileConfig()->getExtensionAttributes()->getDriveFileDestination();

        $filename = $driveConfig->getFilename();
        if (!empty($filename)) {
            $filename = $this->filenameModifier->modify($filename, $exportProcess);
            $filename .= '.' . $exportProcess->getExportResult()->getExtension();
        } else {
            $filename = $exportProcess->getExportResult()->getResultFileName();
        }

        $filePath = trim((string)$driveConfig->getFilePath(), '/');
        if (!$filePath) {
            throw new LocalizedException(__('File Path is not specified.'));
        }

        $tempDirectory = $this->tmp->getTempDirectory($exportProcess->getIdentity());
        $tempFileName = $this->tmp->getResultTempFileName($exportProcess->getIdentity());

        $client = $this->getClient($driveConfig->getKey());
        $service = new \Google_Service_Drive($client);

        $file = new \Google_Service_Drive_DriveFile();
        $file->setName($filename);
        $file->setDescription('Export Document');
        $file->setMimeType($this->mime->getMimeType($tempDirectory->getAbsolutePath($tempFileName)));
        $file->setParents([$this->getResultDirectoryId($filePath, $service)]);

        $service->files->create($file, [
            'data' => $tempDirectory->readFile($tempFileName),
            'mimeType' => $file->getMimeType(),
            'uploadType' => 'media'
        ]);

        $exportProcess->addInfoMessage('The file is successfully uploaded to Google Drive.');
    }

    private function getClient(string $keyFileName): \Google_Client
    {
        $client = new \Google_Client();
        $config = $this->keyFileUploader->getConfigFromFile($keyFileName);

        if (empty($config) || !$this->validateConfig($config)) {
            throw new LocalizedException(__('Wrong Service Account Key File provided.'));
        }

        $client->setApplicationName('Google Drive API');
        $client->setScopes(\Google_Service_Drive::DRIVE);
        $client->setAuthConfig($config);

        $client->fetchAccessTokenWithAssertion();

        return $client;
    }

    private function validateConfig(array $config): bool
    {
        return isset(
            $config['client_id'],
            $config['client_email'],
            $config['private_key']
        );
    }

    private function getResultDirectoryId(string $path, \Google_Service_Drive $service): string
    {
        $explodedPath = explode('/', $path);
        $parentId = '';

        foreach ($explodedPath as $index => $pathPart) {
            if ($index === 0) {
                $parentId = $this->getFirstDirectoryId($pathPart, $service);
                continue;
            }

            $searchedDirectory = $service->files->listFiles(
                [
                    'fields' => 'nextPageToken, files(id, name, parents, mimeType)',
                    'q' => "'$parentId' in parents and name = '$pathPart' "
                        . "and mimeType = 'application/vnd.google-apps.folder'"
                ]
            );

            if ($searchedDirectory->valid()) {
                $parentId = $searchedDirectory->current()->getId();
            } else {
                $file = new \Google_Service_Drive_DriveFile();
                $file->setName($pathPart);
                $file->setMimeType('application/vnd.google-apps.folder');
                $file->setParents([$parentId]);

                $createdFile = $service->files->create($file);
                $parentId = $createdFile->getId();
            }
        }

        return $parentId;
    }

    private function getFirstDirectoryId(string $name, \Google_Service_Drive $service): string
    {
        $searchedDirectories = $service->files->listFiles(
            [
                'fields' => 'nextPageToken, files(id, name, parents, mimeType)',
                'q' => "name = '$name' and mimeType = 'application/vnd.google-apps.folder'"
            ]
        );

        foreach ($searchedDirectories->getFiles() as $directory) {
            if ($directory->getParents() === null) {
                return $directory->getId();
            }
        }

        throw new LocalizedException(__('First folder in the File Path must be shared from root.'));
    }
}
