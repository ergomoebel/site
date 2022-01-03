<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerImport
 */


declare(strict_types=1);

namespace Amasty\CustomerImport\Model\Profile\ResourceModel;

use Amasty\CustomerImport\Model\Profile\Profile as ProfileModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Profile extends AbstractDb
{
    const TABLE_NAME = 'amasty_customer_import_profile';

    protected $_serializableFields = [
        ProfileModel::CUSTOMER_ACTIONS => [null, []]
    ];

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, ProfileModel::ID);
    }
}
