<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExport
 */


declare(strict_types=1);

namespace Amasty\ProductExport\Controller\Adminhtml\Profile;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class Index extends Action
{
    const ADMIN_RESOURCE = 'Amasty_ProductExport::product_export_profiles';

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_ProductExport::product_export_profiles');
        $resultPage->getConfig()->getTitle()->prepend(__('Export Profiles'));
        $resultPage->addBreadcrumb(__('Export Products'), __('Export Profiles'));

        return $resultPage;
    }
}
