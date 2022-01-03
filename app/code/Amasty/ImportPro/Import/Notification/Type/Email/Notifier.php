<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */


declare(strict_types=1);

namespace Amasty\ImportPro\Import\Notification\Type\Email;

use Amasty\ImportCore\Api\ImportProcessInterface;
use Amasty\ImportCore\Api\ImportResultInterface;
use Amasty\ImportPro\Api\Import\NotifierInterface;
use Amasty\ImportPro\Utils\EmailSender;
use Magento\Framework\App\Area;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Notifier implements NotifierInterface
{
    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var EmailSender
     */
    private $emailSender;

    public function __construct(
        TimezoneInterface $timezone,
        EmailSender $emailSender
    ) {
        $this->timezone = $timezone;
        $this->emailSender = $emailSender;
    }

    public function notify(ImportProcessInterface $importProcess): void
    {
        $config = $importProcess->getProfileConfig()->getExtensionAttributes()->getEmailNotificationConfig();
        if ($config && !$config->isAlertEnabled()) {
            return;
        }

        if ($errorMessage = $this->getErrorText($importProcess->getImportResult())) {
            $emailData = [
                'date' => $this->timezone->formatDate(),
                'profile_name' => $importProcess->getProfileConfig()->getExtensionAttributes()->getName(),
                'error_text' => $errorMessage
            ];
            $this->emailSender->sendEmail(
                $config->getAlertRecipients(),
                $config->getAlertTemplate(),
                $config->getAlertSender(),
                $emailData,
                Area::AREA_ADMINHTML
            );
        }
    }

    private function getErrorText(ImportResultInterface $importResult): string
    {
        $errors = [];
        $messages = array_filter(
            array_merge($importResult->getMessages(), $importResult->getPreparedValidationMessages()),
            function ($message) {
                return in_array(
                    $message['type'] ?? '',
                    [ImportResultInterface::MESSAGE_CRITICAL, ImportResultInterface::MESSAGE_ERROR]
                );
            }
        );

        foreach ($messages as $message) {
            if (isset($message['entityMessage'])) {
                $errors[] = $this->prepareEntityMessage($message['messages'] ?? []);
            } elseif (isset($message['message'])) {
                $errors[] = $message['message'];
            }
        }

        return count($errors) > 0 ? implode('. ', $errors) : '';
    }

    private function prepareEntityMessage(array $entityMessages)
    {
        return array_reduce($entityMessages, function ($carry, $message) {
            $errorMessage = isset($message['message'])
                ? $message['message'] . ' in row(s): ' . implode(',', $message['rowNumber'] ?? []) . '. '
                : '';
            $carry .= $errorMessage;

            return $carry;
        }, '');
    }
}
