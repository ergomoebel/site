<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


namespace Amasty\ExportPro\Utils\Email;

use Magento\Framework\ObjectManagerInterface;

class MailMessageFactory
{
    /**
     * Object Manager instance
     *
     * @var ObjectManagerInterface
     */
    protected $objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $instanceName = null;

    public function __construct(
        ObjectManagerInterface $objectManager,
        $instanceName = MailMessage::class
    ) {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    public function create(array $data = []): MailMessage
    {
        return $this->objectManager->create($this->instanceName, $data);
    }
}
