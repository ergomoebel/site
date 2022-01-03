<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */

declare(strict_types=1);

namespace Amasty\ImportPro\Import\FileResolver\Type\GoogleSheet;

use Amasty\ImportCore\Api\FileResolver\FileResolverInterface;
use Amasty\ImportCore\Import\Source\MimeValidator;
use Amasty\ImportCore\Api\ImportProcessInterface;
use Amasty\ImportCore\Import\Utils\TmpFileManagement;
use Magento\Framework\HTTP\Client\Curl;

class FileResolver implements FileResolverInterface
{
    /**
     * @var Curl
     */
    private $curlClient;

    /**
     * @var TmpFileManagement
     */
    private $tmpFileManagement;

    /**
     * @var MimeValidator
     */
    private $mimeValidator;

    public function __construct(
        Curl $curlClient,
        TmpFileManagement $tmpFileManagement,
        MimeValidator $mimeValidator
    ) {
        $this->curlClient = $curlClient;
        $this->tmpFileManagement = $tmpFileManagement;
        $this->mimeValidator = $mimeValidator;
    }

    public function execute(ImportProcessInterface $importProcess): string
    {
        $url = $importProcess->getProfileConfig()->getExtensionAttributes()
            ->getGoogleSheetFileResolver()->getUrl();
        $sourceType = $importProcess->getProfileConfig()->getSourceType();
        if (empty($url)) {
            throw new \RuntimeException('Google Sheet Url couldn\'t be empty.');
        }
        if (false !== strpos($url, '/edit')) {
            $url = preg_replace('/\/edit.*$/is', "/export?format=$sourceType", $url);
        }

        $this->curlClient->setOption(CURLOPT_FOLLOWLOCATION, true);
        $this->curlClient->get($url);

        if ($this->curlClient->getStatus() != 200 && $this->curlClient->getStatus() != 307) {
            switch ($this->curlClient->getStatus()) {
                case 404:
                    throw new \RuntimeException('File Not Found.');
                default:
                    throw new \RuntimeException(
                        'Error occurred while downloading the file. Error code ' . $this->curlClient->getStatus()
                    );
            }
        }

        $fileContent = $this->curlClient->getBody();
        $tmpDir = $this->tmpFileManagement->getTempDirectory($importProcess->getIdentity());
        $fileName = $this->tmpFileManagement->createTempFile($tmpDir);
        $tmpDir->writeFile($fileName, $fileContent);

        $filePath = $tmpDir->getAbsolutePath($fileName);
        if (!$this->mimeValidator->isValid($sourceType, $filePath)) {
            $tmpDir->delete($fileName);
            throw new \RuntimeException('The import file doesn\'t match the selected format.');
        }

        return $filePath;
    }
}
