<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerImport
 */


declare(strict_types=1);

namespace Amasty\CustomerImport\Controller\Adminhtml\Profile;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class Index extends Action
{
    const ADMIN_RESOURCE = 'Amasty_CustomerImport::customer_import_profiles';

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_CustomerImport::customer_import_profiles');
        $resultPage->getConfig()->getTitle()->prepend(__('Import Profiles'));
        $resultPage->addBreadcrumb(__('Import Customers'), __('Import Profiles'));

        return $resultPage;
    }
}
