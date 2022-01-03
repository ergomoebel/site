<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExport
 */


declare(strict_types=1);

namespace Amasty\ProductExport\Controller\Adminhtml\Profile;

use Magento\Backend\App\Action;

class Create extends Action
{
    const ADMIN_RESOURCE = 'Amasty_ProductExport::product_export_profiles';

    public function execute()
    {
        $this->_forward('edit');
    }
}
