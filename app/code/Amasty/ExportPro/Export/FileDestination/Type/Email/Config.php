<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Export\FileDestination\Type\Email;

use Magento\Framework\DataObject;

class Config extends DataObject implements ConfigInterface
{
    const SENDER = 'sender';
    const EMAIL_RECIPIENTS = 'email_recipients';
    const EMAIL_SUBJECT = 'email_subject';
    const EMAIL_TEMPLATE = 'template';

    public function getSender(): ?string
    {
        return $this->getData(self::SENDER);
    }

    public function setSender(?string $sender): ConfigInterface
    {
        $this->setData(self::SENDER, $sender);

        return $this;
    }

    public function getEmailRecipients(): ?string
    {
        return $this->getData(self::EMAIL_RECIPIENTS);
    }

    public function setEmailRecipients(?string $emailRecipients): ConfigInterface
    {
        $this->setData(self::EMAIL_RECIPIENTS, $emailRecipients);

        return $this;
    }

    public function getEmailSubject(): ?string
    {
        return $this->getData(self::EMAIL_SUBJECT);
    }

    public function setEmailSubject(?string $emailSubject): ConfigInterface
    {
        $this->setData(self::EMAIL_SUBJECT, $emailSubject);

        return $this;
    }

    public function getTemplate(): ?string
    {
        return $this->getData(self::EMAIL_TEMPLATE);
    }

    public function setTemplate(?string $template): ConfigInterface
    {
        $this->setData(self::EMAIL_TEMPLATE, $template);

        return $this;
    }
}
