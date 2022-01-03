<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */


namespace Amasty\ImportPro\Controller\Adminhtml\History;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class Index extends Action
{
    const ADMIN_RESOURCE = 'Amasty_ImportPro::import_history_view';

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_ImportPro::importhistory');
        $resultPage->getConfig()->getTitle()->prepend(__('Import History'));
        $resultPage->addBreadcrumb(__('Import History'), __('Import History'));

        return $resultPage;
    }
}
