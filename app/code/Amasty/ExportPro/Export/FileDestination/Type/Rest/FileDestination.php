<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */

declare(strict_types=1);

namespace Amasty\ExportPro\Export\FileDestination\Type\Rest;

use Amasty\ExportCore\Api\ExportProcessInterface;
use Amasty\ExportCore\Api\FileDestination\FileDestinationInterface;
use Amasty\ExportCore\Export\Utils\TmpFileManagement;
use Amasty\ExportPro\Export\FileDestination\Type\Rest\Auth\AuthConfig;
use Amasty\ExportPro\Export\FileDestination\Type\Rest\OptionSource\ContentType;
use Amasty\ExportPro\Export\FileDestination\Type\Rest\OptionSource\Methods;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\ObjectManagerInterface;

class FileDestination implements FileDestinationInterface
{
    /**
     * @var TmpFileManagement
     */
    private $tmpFileManagement;

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

    public function __construct(
        TmpFileManagement $tmpFileManagement,
        Curl $curlClient,
        AuthConfig $authConfig,
        ObjectManagerInterface $objectManager
    ) {
        $this->tmpFileManagement = $tmpFileManagement;
        $this->curlClient = $curlClient;
        $this->authConfig = $authConfig;
        $this->objectManager = $objectManager;
    }

    public function execute(ExportProcessInterface $exportProcess)
    {
        $restConfig = $exportProcess->getProfileConfig()->getExtensionAttributes()->getRestFileDestination();
        $exportProcess->addInfoMessage('Started sending data to Web API Endpoint.');
        $processIdentity = $exportProcess->getIdentity();
        $tempDirectory = $this->tmpFileManagement->getTempDirectory($processIdentity);
        $content = $tempDirectory->readFile(
            $this->tmpFileManagement->getResultTempFileName($processIdentity)
        );
        if ($restConfig->getAuthType() && ($auth = $this->authConfig->get($restConfig->getAuthType()))
            && (!empty($auth['authClass']))
        ) {
            $this->objectManager->create($auth['authClass'])->process($exportProcess, $this->curlClient);
        }
        switch ($restConfig->getContentType()) {
            case ContentType::XML:
                $this->curlClient->addHeader('Content-Type', 'application/xml');
                break;
            default:
                $this->curlClient->addHeader('Content-Type', 'application/json');
                break;
        }
        switch ($restConfig->getMethod()) {
            case Methods::PUT:
                $this->curlClient->setOption(\CURLOPT_CUSTOMREQUEST, 'PUT');
                break;
        }
        $this->curlClient->post($restConfig->getEndpoint(), $content);
        if (!in_array($this->curlClient->getStatus(), [200, 201, 202])) {
            $exportProcess->addErrorMessage(
                $this->curlClient->getBody()
            );
        } else {
            $exportProcess->addInfoMessage('The Data is successfully sent to the Web API Endpoint.');
        }
    }
}
