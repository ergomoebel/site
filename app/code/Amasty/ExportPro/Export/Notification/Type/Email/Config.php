<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Export\Notification\Type\Email;

use Magento\Framework\DataObject;

class Config extends DataObject implements ConfigInterface
{
    const EMAIL_ALERT_ENABLED = 'alert_enabled';
    const EMAIL_SENDER = 'alert_sender';
    const EMAIL_RECIPIENTS = 'alert_recipients';
    const EMAIL_TEMPLATE = 'alert_template';

    public function isAlertEnabled(): bool
    {
        return (bool)$this->getData(self::EMAIL_ALERT_ENABLED);
    }

    public function setIsAlertEnabled(bool $isEnabled): ConfigInterface
    {
        $this->setData(self::EMAIL_ALERT_ENABLED, $isEnabled);

        return $this;
    }

    public function getAlertSender(): ?string
    {
        return $this->getData(self::EMAIL_SENDER);
    }

    public function setAlertSender(string $sender): ConfigInterface
    {
        $this->setData(self::EMAIL_SENDER, $sender);

        return $this;
    }

    public function getAlertTemplate(): ?string
    {
        return $this->getData(self::EMAIL_TEMPLATE);
    }

    public function setAlertTemplate(string $template): ConfigInterface
    {
        $this->setData(self::EMAIL_TEMPLATE, $template);

        return $this;
    }

    public function getAlertRecipients(): ?array
    {
        return $this->getData(self::EMAIL_RECIPIENTS);
    }

    public function setAlertRecipients(array $recipients): ConfigInterface
    {
        $this->setData(self::EMAIL_RECIPIENTS, $recipients);

        return $this;
    }
}
