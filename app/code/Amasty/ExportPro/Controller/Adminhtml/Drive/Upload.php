<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */

declare(strict_types=1);

namespace Amasty\ExportPro\Controller\Adminhtml\Drive;

use Amasty\ExportPro\Export\FileDestination\Type\GoogleDrive\Utils\KeyFileUploader;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class Upload extends Action
{
    /**
     * @var KeyFileUploader
     */
    private $keyFileUploader;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Context $context,
        KeyFileUploader $keyFileUploader,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->keyFileUploader = $keyFileUploader;
        $this->logger = $logger;
    }

    public function execute()
    {
        try {
            $result = $this->keyFileUploader->uploadFile();
        } catch (LocalizedException $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $result = ['error' => __('Something went wrong. Please try again.')];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
