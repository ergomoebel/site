<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderImport
 */


declare(strict_types=1);

namespace Amasty\OrderImport\Controller\Adminhtml\Profile;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class Index extends Action
{
    const ADMIN_RESOURCE = 'Amasty_OrderImport::order_import_profiles';

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_OrderImport::order_import_profiles');
        $resultPage->getConfig()->getTitle()->prepend(__('Import Profiles'));
        $resultPage->addBreadcrumb(__('Import Orders'), __('Import Profiles'));

        return $resultPage;
    }
}
