<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderImport
 */


declare(strict_types=1);

namespace Amasty\OrderImport\Controller\Adminhtml\Profile;

use Magento\Backend\App\Action;

class Create extends Action
{
    const ADMIN_RESOURCE = 'Amasty_OrderImport::order_import_profiles';

    public function execute()
    {
        $this->_forward('edit');
    }
}
