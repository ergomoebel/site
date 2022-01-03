<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


namespace Amasty\ExportPro\Controller\Adminhtml\ExportJob;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class NewAction extends Action
{
    const ADMIN_RESOURCE = 'Amasty_ExportPro::export_job_create';

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_ExportPro::exportcron');
        $resultPage->getConfig()->getTitle()->prepend(__('New Export Cron Job'));
        $resultPage->addBreadcrumb(__('New Export Cron Job'), __('New Export Cron Job'));

        return $resultPage;
    }
}
