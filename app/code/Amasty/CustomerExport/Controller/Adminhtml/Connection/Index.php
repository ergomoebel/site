<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerExport
 */


declare(strict_types=1);

namespace Amasty\CustomerExport\Controller\Adminhtml\Connection;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class Index extends Action
{
    const ADMIN_RESOURCE = 'Amasty_CustomerExport::customer_export_connections';

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_CustoerExport::customer_export');
        $resultPage->getConfig()->getTitle()->prepend(__('3rd Party Links'));
        $resultPage->addBreadcrumb(__('Customer Export'), __('3rd Party Links'));

        return $resultPage;
    }
}
