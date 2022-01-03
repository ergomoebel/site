<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */

declare(strict_types=1);

namespace Amasty\ImportPro\Import\FileResolver\Type\UrlFile;

use Amasty\ImportCore\Api\FileResolver\FileResolverInterface;
use Amasty\ImportCore\Api\ImportProcessInterface;
use Amasty\ImportCore\Import\Source\MimeValidator;
use Amasty\ImportCore\Import\Utils\TmpFileManagement;
use Magento\Framework\HTTP\Client\Curl;

class FileResolver implements FileResolverInterface
{
    /**
     * @var Curl
     */
    private $curlClient;

    /**
     * @var MimeValidator
     */
    private $mimeValidator;

    /**
     * @var TmpFileManagement
     */
    private $tmpFileManagement;

    public function __construct(
        Curl $curlClient,
        MimeValidator $mimeValidator,
        TmpFileManagement $tmpFileManagement
    ) {
        $this->curlClient = $curlClient;
        $this->mimeValidator = $mimeValidator;
        $this->tmpFileManagement = $tmpFileManagement;
    }

    public function execute(ImportProcessInterface $importProcess): string
    {
        $config = $importProcess->getProfileConfig()->getExtensionAttributes()->getUrlFileResolver();
        $url = $config->getUrl();
        if (empty($url)) {
            throw new \RuntimeException('Url couldn\'t be empty.');
        }
        $user = $config->getUser();
        $password = $config->getPassword();
        if (!empty($user) && !empty($password)) {
            $this->curlClient->setCredentials($user, $password);
        }

        $this->curlClient->get($url);

        if ($this->curlClient->getStatus() != 200) {
            switch ($this->curlClient->getStatus()) {
                case 401:
                    throw new \RuntimeException('Basic Auth. Credentials Required.');
                case 404:
                    throw new \RuntimeException('File Not Found.');
                default:
                    throw new \RuntimeException(
                        'Error occurred while downloading the file. Error code: '
                        . $this->curlClient->getStatus()
                    );
            }
        }

        $fileContent = $this->curlClient->getBody();
        $tmpDir = $this->tmpFileManagement->getTempDirectory($importProcess->getIdentity());
        $fileName = $this->tmpFileManagement->createTempFile($tmpDir);
        $tmpDir->writeFile($fileName, $fileContent);

        $filePath = $tmpDir->getAbsolutePath($fileName);
        $sourceType = $importProcess->getProfileConfig()->getSourceType();
        $this->mimeValidator->addMimeType('text/plain');
        if (!$this->mimeValidator->isValid($sourceType, $filePath)) {
            $tmpDir->delete($fileName);
            throw new \RuntimeException('The import file doesn\'t match the selected format.');
        }

        return $filePath;
    }
}
