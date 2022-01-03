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

class Delete extends Action
{
    /**
     * @var KeyFileUploader
     */
    private $keyFileUploader;

    public function __construct(
        Context $context,
        KeyFileUploader $keyFileUploader
    ) {
        parent::__construct($context);
        $this->keyFileUploader = $keyFileUploader;
    }

    public function execute()
    {
        if ($fileHash = $this->getRequest()->getParam('fileHash')) {
            try {
                $result = [];
                $this->keyFileUploader->deleteFile($fileHash);
            } catch (\Exception $e) {
                $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
            }
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

            return $resultJson->setData($result);
        }

        return null;
    }
}
