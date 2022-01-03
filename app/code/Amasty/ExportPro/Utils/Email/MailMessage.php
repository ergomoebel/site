<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


namespace Amasty\ExportPro\Utils\Email;

use Magento\Framework\Mail\Message;
use Zend\Mime\Part;
use Zend\Mime\Mime;

/**
 * Compatibility with Zend Framework 2 (Magento 2.3+)
 */
class MailMessage extends Message
{
    /**
     * @var \Zend\Mail\Message
     */
    protected $zendMessage;

    private $attachments = [];

    public function __construct($charset = 'utf-8')
    {
        $this->zendMessage = new \Zend\Mail\Message();
        $this->zendMessage->setEncoding($charset);
    }

    /**
     * @param string $body
     * @param string $mimeType
     * @param string $disposition
     * @param string $encoding
     * @param null $filename
     *
     * @return Part
     */
    public function createAttachment(
        $body,
        $mimeType = Mime::TYPE_OCTETSTREAM,
        $disposition = Mime::DISPOSITION_ATTACHMENT,
        $encoding = Mime::ENCODING_BASE64,
        $filename = null
    ) {
        $this->setMessageType(self::TYPE_HTML);
        $attachment = new Part($body);
        $attachment->encoding = $encoding;
        $attachment->type = $mimeType;
        $attachment->disposition = $disposition;
        $attachment->filename = $filename;

        $this->attachments[] = $attachment;

        return $attachment;
    }
}
