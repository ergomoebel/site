<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


declare(strict_types=1);

namespace Amasty\OrderExport\Model\Profile\ResourceModel;

use Amasty\OrderExport\Model\Profile\Profile as ProfileModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Profile extends AbstractDb
{
    const TABLE_NAME = 'amasty_order_export_profile';

    protected $_serializableFields = [
        ProfileModel::ORDER_ACTIONS => [null, []]
    ];

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, ProfileModel::ID);
    }
}
