<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Export\Notification\Type\Email;

interface ConfigInterface
{
    /**
     * @return bool
     */
    public function isAlertEnabled(): bool;

    /**
     * @param bool $isEnabled
     * @return \Amasty\ExportPro\Export\Notification\Type\Email\ConfigInterface
     */
    public function setIsAlertEnabled(bool $isEnabled): ConfigInterface;

    /**
     * @return string|null
     */
    public function getAlertSender(): ?string;

    /**
     * @param string $sender
     * @return \Amasty\ExportPro\Export\Notification\Type\Email\ConfigInterface
     */
    public function setAlertSender(string $sender): ConfigInterface;

    /**
     * @return string|null
     */
    public function getAlertTemplate(): ?string;

    /**
     * @param string $template
     * @return \Amasty\ExportPro\Export\Notification\Type\Email\ConfigInterface
     */
    public function setAlertTemplate(string $template): ConfigInterface;

    /**
     * @return string[]|null
     */
    public function getAlertRecipients(): ?array;

    /**
     * @param string[] $recipients
     * @return \Amasty\ExportPro\Export\Notification\Type\Email\ConfigInterface
     */
    public function setAlertRecipients(array $recipients): ConfigInterface;
}
