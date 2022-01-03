<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Export\FileDestination\Type\Email;

use Amasty\ExportCore\Api\ExportProcessInterface;
use Amasty\ExportCore\Api\FileDestination\FileDestinationInterface;
use Amasty\ExportCore\Export\Utils\TmpFileManagement;
use Amasty\ExportPro\Utils\Email\TransportBuilder;
use Magento\Framework\App\Area;
use Magento\Framework\File\Mime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;

class FileDestination implements FileDestinationInterface
{
    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var TmpFileManagement
     */
    private $tmpFileManagement;

    /**
     * @var Mime
     */
    private $mime;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    public function __construct(
        TmpFileManagement $tmpFileManagement,
        TransportBuilder $transportBuilder,
        TimezoneInterface $timezone,
        StoreManagerInterface $storeManager,
        Mime $mime
    ) {
        $this->timezone = $timezone;
        $this->storeManager = $storeManager;
        $this->tmpFileManagement = $tmpFileManagement;
        $this->mime = $mime;
        $this->transportBuilder = $transportBuilder;
    }

    public function execute(ExportProcessInterface $exportProcess)
    {
        $exportProcess->addInfoMessage('Started sending the file to E-mail.');
        $profileConfig = $exportProcess->getProfileConfig();
        $storeId = $this->storeManager->getDefaultStoreView()->getId();
        $emailData = $profileConfig->getExtensionAttributes()->getEmailFileDestination();
        $data = [
            'profile_name' => $profileConfig->getExtensionAttributes()->getName() ?: $profileConfig->getEntityCode(),
            'date'         => $this->timezone->formatDate(),
            'subject'      => $emailData->getEmailSubject()
        ];
        $processIdentity = $exportProcess->getIdentity();
        $tempDirectory = $this->tmpFileManagement->getTempDirectory($processIdentity);
        $content = $tempDirectory->readFile(
            $this->tmpFileManagement->getResultTempFileName($processIdentity)
        );
        $absolutePath =
            $tempDirectory->getAbsolutePath($this->tmpFileManagement->getResultTempFileName($processIdentity));
        $mimeType = $this->mime->getMimeType($absolutePath);
        $fileName = $exportProcess->getExportResult()->getResultFileName();
        $templateIdentifier = $emailData->getTemplate();
        $emailTo = array_filter(explode(',', $emailData->getEmailRecipients()));

        foreach ($emailTo as $reciever) {
            $transportBuild = $this->transportBuilder
                ->setTemplateIdentifier($templateIdentifier)
                ->setTemplateOptions(['area' => Area::AREA_ADMINHTML, 'store' => $storeId])
                ->setTemplateVars($data)
                ->setFromByScope($emailData->getSender())
                ->addTo($reciever)
                ->setSubject($emailData->getEmailSubject())
                ->addAttachment($content, $fileName, $mimeType);
            $transportBuild->getTransport()->sendMessage();
        }
        $exportProcess->addInfoMessage('The file is successfully sent to the E-mail.');
    }
}
