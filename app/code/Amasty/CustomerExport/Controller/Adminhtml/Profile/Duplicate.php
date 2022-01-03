<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerExport
 */


declare(strict_types=1);

namespace Amasty\CustomerExport\Controller\Adminhtml\Profile;

use Magento\Backend\App\Action;

class Duplicate extends Action
{
    const ADMIN_RESOURCE = 'Amasty_CustomerExport::customer_export_profiles';

    const REQUEST_PARAM_NAME = 'duplicate';

    public function execute()
    {
        $this->_forward('edit', null, null, [self::REQUEST_PARAM_NAME => true]);
    }
}
