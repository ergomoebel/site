<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


declare(strict_types=1);

namespace Amasty\OrderExport\Model\Connection\ResourceModel;

use Amasty\OrderExport\Model\Connection\Connection as CoonectionModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Connection extends AbstractDb
{
    const TABLE_NAME = 'amasty_order_export_connection';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, CoonectionModel::ID);
    }
}
