<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */

declare(strict_types=1);

namespace Amasty\ImportPro\Import\FileResolver\Type\GoogleDrive;

use Amasty\ImportCore\Api\FileResolver\FileResolverInterface;
use Amasty\ImportCore\Api\ImportProcessInterface;
use Amasty\ImportCore\Import\Source\MimeValidator;
use Amasty\ImportCore\Import\Utils\TmpFileManagement;
use Magento\Framework\Filesystem\Io\File;

class FileResolver implements FileResolverInterface
{
    /**
     * @var TmpFileManagement
     */
    private $tmpFileManagement;

    /**
     * @var MimeValidator
     */
    private $mimeValidator;

    /**
     * @var Utils\KeyFileUploader
     */
    private $keyFileUploader;

    /**
     * @var File
     */
    private $ioFile;

    public function __construct(
        TmpFileManagement $tmpFileManagement,
        MimeValidator $mimeValidator,
        Utils\KeyFileUploader $keyFileUploader,
        File $ioFile
    ) {
        $this->tmpFileManagement = $tmpFileManagement;
        $this->mimeValidator = $mimeValidator;
        $this->ioFile = $ioFile;
        $this->keyFileUploader = $keyFileUploader;
    }

    public function execute(ImportProcessInterface $importProcess): string
    {
        $config = $importProcess->getProfileConfig()->getExtensionAttributes()->getDriveFileResolver();

        $filePath = trim((string)$config->getFilePath(), '/');
        if (empty($filePath)) {
            throw new \RuntimeException('File Path is empty.');
        }

        $client = $this->getClient($config->getKey());
        $service = new \Google_Service_Drive($client);
        $driveFile = $this->getDriveFileByPath($filePath, $service);

        $tmpDir = $this->tmpFileManagement->getTempDirectory($importProcess->getIdentity());
        $fileName = $this->tmpFileManagement->createTempFile($tmpDir);

        $fileContent = $service->files->get(
            $driveFile->getId(),
            ['alt' => 'media', 'mimeType' => $driveFile->getMimeType()]
        )->getBody()->getContents();
        $tmpDir->writeFile($fileName, $fileContent);

        $filePath = $tmpDir->getAbsolutePath($fileName);
        $sourceType = $importProcess->getProfileConfig()->getSourceType();
        if (!$this->mimeValidator->isValid($sourceType, $filePath)) {
            $tmpDir->delete($fileName);
            throw new \RuntimeException('The import file doesn\'t match the selected format.');
        }

        return $filePath;
    }

    private function getClient(string $keyFileName): \Google_Client
    {
        $client = new \Google_Client();
        $config = $this->keyFileUploader->getConfigFromFile($keyFileName);

        if (empty($config) || !$this->validateConfig($config)) {
            throw new \RuntimeException('Wrong Service Account Key File provided.');
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

    private function getDriveFileByPath(
        string $path,
        \Google_Service_Drive $service
    ): \Google_Service_Drive_DriveFile {
        $explodedPath = explode('/', $path);
        $lastIndex = count($explodedPath) - 1;
        $parentId = '';

        foreach ($explodedPath as $index => $pathPart) {
            if ($index !== $lastIndex) {
                if ($index === 0) {
                    $parentId = $this->getFirstDirectoryId($pathPart, $service);
                    continue;
                }
                $searchResult = $service->files->listFiles(
                    [
                        'fields' => 'nextPageToken, files(id, name, parents, mimeType)',
                        'q' => "'$parentId' in parents and name = '$pathPart' "
                            . "and mimeType = 'application/vnd.google-apps.folder'"
                    ]
                );
            } else {
                $searchResult = $service->files->listFiles(
                    [
                        'fields' => 'nextPageToken, files(id, name, parents, mimeType)',
                        'q' => "'$parentId' in parents and name = '$pathPart'"
                    ]
                );
            }
            if (!$searchResult->valid()) {
                throw new \RuntimeException("The import file with path $path doesn't exist.");
            } elseif ($index === $lastIndex) {
                return $searchResult->current();
            } else {
                $parentId = $searchResult->current()->getId();
            }
        }
        throw new \RuntimeException("The import file with path $path doesn't exist.");
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

        throw new \RuntimeException('First folder in the File Path must be shared from root.');
    }
}
