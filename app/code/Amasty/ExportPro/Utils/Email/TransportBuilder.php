<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Utils\Email;

use Magento\Framework\Mail\EmailMessageInterface;
use Magento\Framework\Mail\EmailMessageInterfaceFactory;
use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\Mail\MessageInterfaceFactory;
use Magento\Framework\Mail\MimeMessageInterfaceFactory;
use Magento\Framework\Mail\Template\FactoryInterface;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Mail\Template\TransportBuilder as ParentTransportBuilder;
use Magento\Framework\Mail\TransportInterfaceFactory;
use Magento\Framework\ObjectManagerInterface;

class TransportBuilder extends ParentTransportBuilder
{
    /**
     * @var array
     */
    private $attachments = [];

    /**
     * @var array
     */
    private $messageData;

    /**
     * @var EmailMessageInterfaceFactory
     */
    private $emailMessageInterfaceFactory;

    /**
     * @var MimeMessageInterfaceFactory
     */
    private $mimeMessageInterfaceFactory;

    /**
     * @var MessageInterfaceFactory
     */
    private $messageFactory;

    public function __construct(
        FactoryInterface $templateFactory,
        MessageInterface $message,
        MessageInterfaceFactory $messageFactory,
        SenderResolverInterface $senderResolver,
        ObjectManagerInterface $objectManager,
        TransportInterfaceFactory $mailTransportFactory
    ) {
        parent::__construct(
            $templateFactory,
            $message,
            $senderResolver,
            $objectManager,
            $mailTransportFactory
        );
        $this->message = $message;
        $this->messageFactory = $messageFactory;
        if (interface_exists(EmailMessageInterface::class)) {
            $this->message = $objectManager->create(MailMessage::class);
            $this->messageFactory = $objectManager->create(MailMessageFactory::class);
            $this->mimeMessageInterfaceFactory = $objectManager->create(MimeMessageInterfaceFactory::class);
            $this->emailMessageInterfaceFactory = $objectManager->create(EmailMessageInterfaceFactory::class);
        }
    }

    public function addAttachment(string $content, string $name, string $type = 'text/plain'): self
    {
        $attachment = $this->message->createAttachment(
            $content,
            $type,
            \Zend_Mime::DISPOSITION_ATTACHMENT,
            \Zend_Mime::ENCODING_BASE64,
            $name
        );

        $this->attachments[] = $attachment;

        return $this;
    }

    protected function prepareMessage(): self
    {
        parent::prepareMessage();
        if ($this->mimeMessageInterfaceFactory !== null) {
            $parts = $this->message->getBody()->getParts();

            $this->messageData['body'] = $this->mimeMessageInterfaceFactory->create(
                ['parts' => array_merge($parts, $this->attachments)]
            );

            $this->messageData['to'] = $this->message->getTo();
            $this->messageData['from'] = $this->message->getFrom();

            if (!isset($this->messageData['subject'])) {
                $this->messageData['subject'] = $this->message->getSubject();
            }

            $this->message = $this->emailMessageInterfaceFactory->create($this->messageData);
        }

        return $this;
    }

    public function setSubject(string $subject): self
    {
        $this->messageData['subject'] = $subject;

        return $this;
    }

    public function setFromByScope($from, $scopeId = null): self
    {
        $from = $this->_senderResolver->resolve($from, $scopeId);

        if ($this->mimeMessageInterfaceFactory !== null) {
            return parent::setFromByScope($from, $scopeId);
        } else {
            $this->message->setFrom($from['email'], $from['name']);
        }

        return $this;
    }

    protected function reset(): self
    {
        parent::reset();
        $this->message = $this->messageFactory->create();
        $this->messageData = [];
        $this->attachments = [];

        return $this;
    }
}
