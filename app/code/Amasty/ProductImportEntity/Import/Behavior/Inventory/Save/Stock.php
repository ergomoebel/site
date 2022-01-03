<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\Behavior\Inventory\Save;

class Stock extends AbstractInventory
{
    protected $identityKey = 'stock_id';

    protected function getMainTable()
    {
        return 'inventory_stock';
    }
}
