<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */

declare(strict_types=1);

namespace Amasty\ImportPro\Import\FileResolver\Type\Rest;

use Amasty\ImportCore\Api\FileResolver\FileResolverInterface;
use Amasty\ImportCore\Api\ImportProcessInterface;
use Amasty\ImportCore\Import\Utils\TmpFileManagement;
use Amasty\ImportPro\Import\FileResolver\Type\Rest\Auth\AuthConfig;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\ObjectManagerInterface;

class FileResolver implements FileResolverInterface
{
    /**
     * @var Curl
     */
    private $curlClient;

    /**
     * @var AuthConfig
     */
    private $authConfig;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var TmpFileManagement
     */
    private $tmpFileManagement;

    public function __construct(
        Curl $curlClient,
        AuthConfig $authConfig,
        ObjectManagerInterface $objectManager,
        TmpFileManagement $tmpFileManagement
    ) {
        $this->curlClient = $curlClient;
        $this->authConfig = $authConfig;
        $this->objectManager = $objectManager;
        $this->tmpFileManagement = $tmpFileManagement;
    }

    public function execute(ImportProcessInterface $importProcess): string
    {
        $restConfig = $importProcess->getProfileConfig()->getExtensionAttributes()->getRestFileResolver();
        $importProcess->addInfoMessage('Started fetching data from Web API Endpoint.');
        if ($restConfig->getAuthType() && ($auth = $this->authConfig->get($restConfig->getAuthType()))
            && (!empty($auth['authClass']))
        ) {
            $this->objectManager->create($auth['authClass'])->process($importProcess, $this->curlClient);
        }

        $this->curlClient->get($restConfig->getEndpoint());
        $fileContent = $this->curlClient->getBody();
        if (!in_array($this->curlClient->getStatus(), [200, 201, 202])) {
            $importProcess->addErrorMessage(
                $this->curlClient->getBody()
            );
        } else {
            $importProcess->addInfoMessage('The Data is successfully fetched from Web API Endpoint.');
        }

        $tmpDir = $this->tmpFileManagement->getTempDirectory($importProcess->getIdentity());
        $fileName = $this->tmpFileManagement->createTempFile($tmpDir);
        $tmpDir->writeFile($fileName, \json_decode($fileContent));

        return $tmpDir->getAbsolutePath($fileName);
    }
}
