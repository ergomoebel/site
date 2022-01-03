<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\Behavior\Product\Save\SuperAttribute;

use Amasty\ProductImportEntity\Import\Behavior\Product\AbstractScopedBehavior;

class SuperAttributeLabel extends AbstractScopedBehavior
{
    /**
     * @inheritDoc
     */
    protected function getMainTable()
    {
        return 'catalog_product_super_attribute_label';
    }

    /**
     * @inheritDoc
     */
    protected function getScopedKeys()
    {
        return ['value', 'use_default'];
    }
}
