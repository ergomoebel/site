<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_InventoryExportEntity
 */

declare(strict_types=1);

namespace Amasty\InventoryExportEntity\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class StockSalesChannel extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('inventory_stock_sales_channel', 'code');
    }
}
