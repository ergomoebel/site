<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExportEntity
 */

declare(strict_types=1);

namespace Amasty\ProductExportEntity\Export\Product\ScopedEntity;

use Amasty\ProductExportEntity\Model\ResourceModel\ScopedEntity\AbstractCollector;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Data\Collection\AbstractDb;

class ProductCollector extends AbstractCollector
{
    protected function performCollect(AbstractDb $collection, array $defaultItems, $scopeValue)
    {
        $items = [];
        /** @var Product $product */
        foreach ($collection as $product) {
            $items[] = $this->prepareEntityData(
                $product->getData(),
                $defaultItems,
                'entity_id',
                self::STORE_ID_FIELD,
                $scopeValue
            );
        }
        return $items;
    }

    /**
     * Prepare products collection
     *
     * @param Collection|AbstractDb $collection
     * @param int $scopeValue
     * @return Collection|AbstractDb
     */
    protected function prepareCollection(AbstractDb $collection, $scopeValue)
    {
        $collection->addOrder('entity_id', Collection::SORT_ORDER_ASC)
            ->setStoreId($scopeValue);
        return parent::prepareCollection($collection, $scopeValue);
    }
}
