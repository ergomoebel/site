<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\Behavior\Product\Save\Bundle;

use Amasty\ProductImportEntity\Import\Behavior\Product\AbstractScopedBehavior;

class BundleOptionValue extends AbstractScopedBehavior
{
    /**
     * @inheritDoc
     */
    protected function insertData(array $data, string $tableName)
    {
        $this->getConnection()->insertOnDuplicate($tableName, $data, ['title']);
    }

    /**
     * @inheritDoc
     */
    protected function getMainTable()
    {
        return 'catalog_product_bundle_option_value';
    }

    /**
     * @inheritDoc
     */
    protected function getScopedKeys()
    {
        return ['title', 'parent_product_id'];
    }
}
