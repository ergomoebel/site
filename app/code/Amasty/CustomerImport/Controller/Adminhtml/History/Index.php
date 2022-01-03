<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerImport
 */


namespace Amasty\CustomerImport\Controller\Adminhtml\History;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\ResultFactory;

class Index extends Action
{
    const ADMIN_RESOURCE = 'Amasty_CustomerImport::customer_import_history';

    /**
     * Index action
     *
     * @return Page
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_CustomerImport::customer_import_history');
        $resultPage->getConfig()->getTitle()->prepend(__('Import History'));
        $resultPage->addBreadcrumb(__('Import History'), __('Import History'));

        return $resultPage;
    }
}
