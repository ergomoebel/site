<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerExport
 */


declare(strict_types=1);

namespace Amasty\CustomerExport\Model\Profile\ResourceModel;

use Amasty\CustomerExport\Model\Profile\Profile as ProfileModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Profile extends AbstractDb
{
    const TABLE_NAME = 'amasty_customer_export_profile';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, ProfileModel::ID);
    }
}
