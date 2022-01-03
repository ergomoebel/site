<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */

declare(strict_types=1);

namespace Amasty\ImportPro\Import\FileResolver\Type\Dropbox;

use Amasty\ImportCore\Api\FileResolver\FileResolverInterface;
use Amasty\ImportCore\Api\ImportProcessInterface;
use Amasty\ImportCore\Import\Source\MimeValidator;
use Amasty\ImportCore\Import\Utils\TmpFileManagement;
use Magento\Framework\Filesystem\Io\File;
use Spatie\Dropbox\Client;

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
     * @var File
     */
    private $ioFile;

    public function __construct(
        TmpFileManagement $tmpFileManagement,
        MimeValidator $mimeValidator,
        File $ioFile
    ) {
        $this->tmpFileManagement = $tmpFileManagement;
        $this->mimeValidator = $mimeValidator;
        $this->ioFile = $ioFile;
    }

    public function execute(ImportProcessInterface $importProcess): string
    {
        $config = $importProcess->getProfileConfig()->getExtensionAttributes()->getDropboxFileResolver();

        $filePath = $config->getFilePath();
        if (empty($filePath)) {
            throw new \RuntimeException('File Path is empty.');
        }

        $client = new Client($config->getToken());
        try {
            $client->getAccountInfo(); //for access token validation
        } catch (\Exception $e) {
            throw new \RuntimeException("Wrong Access Token.");
        }

        try {
            $file = $client->download($filePath);
        } catch (\Exception $e) {
            throw new \RuntimeException("The import file with path $filePath doesn't exist.");
        }

        $tmpDir = $this->tmpFileManagement->getTempDirectory($importProcess->getIdentity());
        $fileName = $this->tmpFileManagement->createTempFile($tmpDir);
        $filePath = $tmpDir->getAbsolutePath($fileName);

        $this->ioFile->write($filePath, $file);

        $sourceType = $importProcess->getProfileConfig()->getSourceType();
        if (!$this->mimeValidator->isValid($sourceType, $filePath)) {
            $tmpDir->delete($fileName);
            throw new \RuntimeException('The import file doesn\'t match the selected format.');
        }

        return $filePath;
    }
}
