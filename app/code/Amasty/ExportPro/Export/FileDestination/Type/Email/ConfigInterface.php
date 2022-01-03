<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Export\FileDestination\Type\Email;

interface ConfigInterface
{
    /**
     * @return string|null
     */
    public function getSender(): ?string;

    /**
     * @param string|null $sender
     *
     * @return \Amasty\ExportPro\Export\FileDestination\Type\Email\ConfigInterface
     */
    public function setSender(?string $sender): ConfigInterface;

    /**
     * @return string|null
     */
    public function getEmailRecipients(): ?string;

    /**
     * @param string|null $emailRecipients
     *
     * @return \Amasty\ExportPro\Export\FileDestination\Type\Email\ConfigInterface
     */
    public function setEmailRecipients(?string $emailRecipients): ConfigInterface;

    /**
     * @return string|null
     */
    public function getEmailSubject(): ?string;

    /**
     * @param string|null $emailSubject
     *
     * @return \Amasty\ExportPro\Export\FileDestination\Type\Email\ConfigInterface
     */
    public function setEmailSubject(?string $emailSubject): ConfigInterface;

    /**
     * @return string|null
     */
    public function getTemplate(): ?string;

    /**
     * @param string|null $template
     *
     * @return \Amasty\ExportPro\Export\FileDestination\Type\Email\ConfigInterface
     */
    public function setTemplate(?string $template): ConfigInterface;
}
