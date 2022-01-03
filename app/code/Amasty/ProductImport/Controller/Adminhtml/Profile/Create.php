<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImport
 */


declare(strict_types=1);

namespace Amasty\ProductImport\Controller\Adminhtml\Profile;

use Magento\Backend\App\Action;

class Create extends Action
{
    const ADMIN_RESOURCE = 'Amasty_ProductImport::product_import_profiles';

    public function execute()
    {
        $this->_forward('edit');
    }
}
