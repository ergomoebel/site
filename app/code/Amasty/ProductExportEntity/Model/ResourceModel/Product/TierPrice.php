<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExportEntity
 */

declare(strict_types=1);

namespace Amasty\ProductExportEntity\Model\ResourceModel\Product;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class TierPrice extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('catalog_product_entity_tier_price', 'value_id');
    }
}
