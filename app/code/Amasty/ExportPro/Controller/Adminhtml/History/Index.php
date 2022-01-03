<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


namespace Amasty\ExportPro\Controller\Adminhtml\History;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class Index extends Action
{
    const ADMIN_RESOURCE = 'Amasty_ExportPro::export_history_view';

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_ExportPro::exporthistory');
        $resultPage->getConfig()->getTitle()->prepend(__('Export History'));
        $resultPage->addBreadcrumb(__('Export History'), __('Export History'));

        return $resultPage;
    }
}
