<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExportEntity
 */

declare(strict_types=1);

namespace Amasty\ProductExportEntity\Model\ResourceModel\Product\Type\Bundle\Option;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Value extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('catalog_product_bundle_option_value', 'value_id');
    }
}
