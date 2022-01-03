<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Export\Notification\Type\Email;

use Amasty\ExportCore\Api\ExportProcessInterface;
use Amasty\ExportCore\Api\ExportResultInterface;
use Amasty\ExportPro\Api\Export\NotifierInterface;
use Amasty\ExportPro\Utils\Email\TransportBuilder;
use Magento\Framework\App\Area;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\Store;

class Notifier implements NotifierInterface
{
    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    public function __construct(
        TimezoneInterface $timezone,
        TransportBuilder $transportBuilder
    ) {
        $this->timezone = $timezone;
        $this->transportBuilder = $transportBuilder;
    }

    public function notify(ExportProcessInterface $exportProcess): void
    {
        $config = $exportProcess->getProfileConfig()->getExtensionAttributes()->getEmailNotificationConfig();

        if ($config && !$config->isAlertEnabled()) {
            return;
        }

        $emailData = [
            'date' => $this->timezone->formatDate(),
            'profile_name' => $exportProcess->getProfileConfig()->getExtensionAttributes()->getName(),
            'error_text' => $this->getTextMessages($exportProcess->getExportResult())
        ];

        foreach ($config->getAlertRecipients() as $recipient) {
            $transportBuild = $this->transportBuilder
                ->setTemplateIdentifier($config->getAlertTemplate())
                ->setTemplateOptions(['area' => Area::AREA_ADMINHTML, 'store' => Store::DEFAULT_STORE_ID])
                ->setTemplateVars($emailData)
                ->setFromByScope($config->getAlertSender(), Store::DEFAULT_STORE_ID)
                ->addTo($recipient);
            $transportBuild->getTransport()->sendMessage();
        }
    }

    private function getTextMessages(ExportResultInterface $exportResult): string
    {
        $messages = array_filter($exportResult->getMessages(), function ($message) {
            return in_array(
                $message['type'],
                [ExportResultInterface::MESSAGE_CRITICAL, ExportResultInterface::MESSAGE_ERROR]
            );
        });

        return array_reduce($messages, function ($carry, $message) {
            $carry .= $message['message'] . ' ';

            return $carry;
        }, '');
    }
}
