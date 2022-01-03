<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExportEntity
 */

declare(strict_types=1);

namespace Amasty\OrderExportEntity\Model\Indexer\CustomOption\Action;

use Magento\Catalog\Api\Data\ProductCustomOptionInterface;
use Magento\Catalog\Model\Product\Option;
use Magento\Catalog\Model\ResourceModel\Product\Option\Collection as ProductOptionCollection;
use Magento\Catalog\Model\ResourceModel\Product\Option\CollectionFactory as ProductOptionCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection as OrderItemCollection;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as OrderItemCollectionFactory;
use Magento\Sales\Model\Order\Item;

/**
 * Custom options indexer data provider
 */
class DataProvider
{
    /**
     * Default option type to resolve
     */
    const DEFAULT_OPTION_TYPE = ProductCustomOptionInterface::OPTION_TYPE_FIELD;

    /**
     * Custom option types without option values
     */
    const SIMPLE_CUSTOM_OPTION_TYPES = [
        ProductCustomOptionInterface::OPTION_TYPE_FIELD,
        ProductCustomOptionInterface::OPTION_GROUP_TEXT
    ];

    /**
     * Order item's options required keys
     */
    const OPTIONS_REQUIRED_KEYS = ['value', 'label', 'option_id'];

    /**
     * @var OrderItemCollectionFactory
     */
    private $orderItemCollectionFactory;

    /**
     * @var ProductOptionCollectionFactory
     */
    private $productOptionCollectionFactory;

    /**
     * @var array
     */
    private $productOptionsByProductIdAndStoreId = [];

    public function __construct(
        OrderItemCollectionFactory $orderItemCollectionFactory,
        ProductOptionCollectionFactory $productOptionCollectionFactory
    ) {
        $this->orderItemCollectionFactory = $orderItemCollectionFactory;
        $this->productOptionCollectionFactory = $productOptionCollectionFactory;
    }

    /**
     * Get order items data for index
     *
     * @param array $orderItemIds
     * @param int $lastOrderItemId
     * @param int $batchSize
     * @return Item[]
     */
    public function getOrderItemsForIndex(
        array $orderItemIds = [],
        $lastOrderItemId = 0,
        $batchSize = 200
    ) {
        /** @var OrderItemCollection $collection */
        $collection = $this->orderItemCollectionFactory->create();
        $collection->addFieldToFilter('main_table.item_id', ['gt' => $lastOrderItemId])
            ->addFieldToFilter('main_table.item_id', ['lteq' => $lastOrderItemId + $batchSize])
            ->addOrder('main_table.item_id', OrderItemCollection::SORT_ORDER_ASC);
        if (count($orderItemIds)) {
            $collection->addFieldToFilter('main_table.item_id', ['in' => $orderItemIds]);
        }

        /**
         * @param Item $orderItem
         * @return bool
         */
        $filterAvailableForIndexCallback = function ($orderItem) {
            $options = $orderItem->getProductOptionByCode('options');

            return is_array($options);
        };
        return array_filter($collection->getItems(), $filterAvailableForIndexCallback);
    }

    /**
     * Prepare custom options index
     *
     * @param Item $orderItem
     * @return array
     */
    public function prepareCustomOptionsIndex(Item $orderItem)
    {
        $indexData = [];

        $itemStoreId = $orderItem->getStoreId();
        foreach ($orderItem->getProductOptionByCode('options') as $option) {
            if ($this->isOptionDataValidForIndex($option)) {
                $indexRow = [
                    'order_item_id' => $orderItem->getId(),
                    'option_title' => $option['label'],
                    'option_value' => $option['value'],
                    'price' => null,
                    'sku' => null
                ];

                $optionType = $option['option_type'] ?? self::DEFAULT_OPTION_TYPE;
                if (in_array($optionType, self::SIMPLE_CUSTOM_OPTION_TYPES)) {
                    $productOptions = $this->getProductOptions($orderItem->getProductId(), $itemStoreId);
                    $productOption = $productOptions[$option['option_id']] ?? null;
                    if ($productOption) {
                        $indexRow['price'] = $productOption->getPrice();
                        $indexRow['sku'] = $productOption->getSku();
                    }
                }

                $indexData[] = $indexRow;
            }
        }

        return $indexData;
    }

    /**
     * Checks if option data is valid for indexing
     *
     * @param array $option
     * @return bool
     */
    private function isOptionDataValidForIndex(array $option)
    {
        foreach (self::OPTIONS_REQUIRED_KEYS as $key) {
            if (!isset($option[$key])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get product options by product Id and store Id
     *
     * @param int $productId
     * @param int $storeId
     * @return Option[]
     */
    private function getProductOptions($productId, $storeId)
    {
        $key = $productId . '-' . $storeId;
        if (!isset($this->productOptionsByProductIdAndStoreId[$key])) {
            /** @var ProductOptionCollection $productOptionCollection */
            $productOptionCollection = $this->productOptionCollectionFactory->create();
            $this->productOptionsByProductIdAndStoreId[$key] = $productOptionCollection->getProductOptions(
                $productId,
                $storeId
            );
        }
        return $this->productOptionsByProductIdAndStoreId[$key];
    }
}
