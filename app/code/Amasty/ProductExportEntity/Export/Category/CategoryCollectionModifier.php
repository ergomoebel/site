<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExportEntity
 */

declare(strict_types=1);

namespace Amasty\ProductExportEntity\Export\Category;

use Amasty\ExportCore\Api\CollectionModifierInterface;
use Magento\Framework\Data\Collection;

class CategoryCollectionModifier implements CollectionModifierInterface
{
    public function apply(Collection $collection): CollectionModifierInterface
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $collection */
        $collection->joinField(
            'product_id',
            'catalog_category_product',
            'product_id',
            'category_id = entity_id',
            null
        );
        $collection->getSelect()
            ->group('e.entity_id');

        return $this;
    }
}
