<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExportEntity
 */

declare(strict_types=1);

namespace Amasty\OrderExportEntity\Model\Indexer\Attribute\Action;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;

class Full
{
    /**
     * @var CollectionFactory
     */
    private $productAttributeCollectionFactory;

    /**
     * @var IndexIteratorFactory
     */
    private $iteratorFactory;

    public function __construct(
        CollectionFactory $productAttributeCollectionFactory,
        IndexIteratorFactory $indexIteratorFactory
    ) {
        $this->productAttributeCollectionFactory = $productAttributeCollectionFactory;
        $this->iteratorFactory = $indexIteratorFactory;
    }

    /**
     * Rebuild index
     *
     * @param array $attributesHash
     * @param array $itemsIds
     * @return IndexIterator
     */
    public function rebuildIndex(array $attributesHash, $itemsIds = null)
    {
        $splitedAttributeCodes = [
            'static' => [],
            'dynamic' => []
        ];

        /** @var Collection $collection */
        $collection = $this->productAttributeCollectionFactory->create();
        $collection->addFieldToFilter(
            'attribute_code',
            ['in' => array_values($attributesHash)]
        );

        foreach ($collection as $attribute) {
            $key = $attribute->getBackendType() == Attribute::TYPE_STATIC
                ? 'static'
                : 'dynamic';
            $splitedAttributeCodes[$key][] = $attribute->getAttributeCode();
        }

        return $this->iteratorFactory->create([
            'itemsIds' => $itemsIds,
            'staticFields' => $splitedAttributeCodes['static'],
            'attributes' =>  $splitedAttributeCodes['dynamic']
        ]);
    }
}
