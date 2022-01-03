<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExportEntity
 */

declare(strict_types=1);

namespace Amasty\ProductExportEntity\Export\Category\ScopedEntity;

use Amasty\ProductExportEntity\Model\ResourceModel\ScopedEntity\AbstractCollector;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Framework\Data\Collection\AbstractDb;

class CategoryCollector extends AbstractCollector
{
    protected function performCollect(AbstractDb $collection, array $defaultItems, $scopeValue)
    {
        $items = [];
        /** @var Category $category */
        foreach ($collection as $category) {
            $items[] = $this->prepareEntityData(
                $category->getData(),
                $defaultItems,
                'entity_id',
                self::STORE_ID_FIELD,
                $scopeValue
            );
        }
        return $items;
    }

    /**
     * Prepare category collection
     *
     * @param AbstractDb|Collection $collection
     * @param int $scopeValue
     * @return AbstractDb|Collection
     */
    protected function prepareCollection(AbstractDb $collection, $scopeValue)
    {
        $collection->addOrder('entity_id', Collection::SORT_ORDER_ASC)
            ->setStoreId($scopeValue);

        return parent::prepareCollection($collection, $scopeValue);
    }
}
