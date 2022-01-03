<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\Behavior\Inventory\Save;

class SourceStockLink extends AbstractInventory
{
    protected $identityKey = 'link_id';

    protected function getMainTable()
    {
        return 'inventory_source_stock_link';
    }
}
