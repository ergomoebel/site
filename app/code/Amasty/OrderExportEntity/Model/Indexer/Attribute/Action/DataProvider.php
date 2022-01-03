<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExportEntity
 */

declare(strict_types=1);

namespace Amasty\OrderExportEntity\Model\Indexer\Attribute\Action;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection as OrderItemCollection;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as OrderItemCollectionFactory;
use Magento\Sales\Model\Order\Item;
use Magento\Store\Model\Store;

class DataProvider
{
    const DEFAULT_PAGE_SIZE = 200;

    /**
     * Attribute codes with options for which option values will be indexed
     * instead of option labels
     */
    const INDEX_VALUE_ATTRIBUTES = ['status'];

    /**
     * @var OrderItemCollectionFactory
     */
    private $orderItemCollectionFactory;

    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var EavConfig
     */
    private $eavConfig;

    /**
     * @var Product[]
     */
    private $orderItemProducts = [];

    /**
     * @var array
     */
    private $attributeOptions = [];

    public function __construct(
        OrderItemCollectionFactory $orderItemCollectionFactory,
        ProductCollectionFactory $productCollectionFactory,
        EavConfig $eavConfig
    ) {
        $this->orderItemCollectionFactory = $orderItemCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->eavConfig = $eavConfig;
    }

    /**
     * Get order items data for index
     *
     * @param array $staticFields
     * @param array $fields
     * @param array $itemIds
     * @param int $page
     * @param int $pageSize
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getOrderItemsForIndex(
        array $staticFields,
        array $fields,
        array $itemIds = [],
        int $page = 1,
        int $pageSize = self::DEFAULT_PAGE_SIZE
    ) {
        $result = [];

        /** @var OrderItemCollection $collection */
        $collection = $this->orderItemCollectionFactory->create();
        $collection->setPage($page, $pageSize)
            ->addOrder('main_table.item_id', OrderItemCollection::SORT_ORDER_ASC);
        if (count($itemIds)) {
            $collection->addFieldToFilter('main_table.item_id', ['in' => $itemIds]);
        }

        if ($page <= $collection->getLastPageNumber()) {
            foreach ($this->groupByStoreId($collection->getItems()) as $storeId => $orderItems) {
                $products = $this->getOrderItemsProducts(
                    $orderItems,
                    $staticFields,
                    $fields,
                    $storeId
                );

                foreach ($orderItems as $orderItem) {
                    $productId = $orderItem->getProductId();

                    if (isset($products[$productId])) {
                        $result[] = $this->prepareIndexRow(
                            $staticFields,
                            $fields,
                            $orderItem,
                            $products[$productId]
                        );
                    }
                }
            }
        }

        $this->clearOrderItemProducts();

        return $result;
    }

    /**
     * Group order items by store Id
     *
     * @param Item[] $orderItems
     * @return Item[][]
     */
    private function groupByStoreId(array $orderItems)
    {
        $result = [];
        foreach ($orderItems as $orderItem) {
            $result[$orderItem->getStoreId()][] = $orderItem;
        }
        return $result;
    }

    /**
     * Get order items products
     *
     * @param Item[] $orderItems
     * @param array $staticFields
     * @param array $attributeCodes
     * @param int $storeId
     * @return Product[]
     */
    private function getOrderItemsProducts(
        array $orderItems,
        array $staticFields,
        array $attributeCodes,
        $storeId
    ) {
        $result = [];

        $keys = $this->getOrderItemsProductKeys(
            $storeId,
            $this->getOrderItemsProductIds($orderItems)
        );
        $keysToLoad = array_diff_key($keys, $this->orderItemProducts);
        if (!empty($keysToLoad)) {
            $productIds = array_column($keysToLoad, 'product_id');

            /** @var ProductCollection $collection */
            $collection = $this->productCollectionFactory->create();
            $collection->addIdFilter($productIds)
                ->addIdFilter($productIds);

            foreach ($staticFields as $staticField) {
                $collection->addFieldToSelect($staticField);
            }
            foreach ($attributeCodes as $attributeCode) {
                $collection->addAttributeToSelect($attributeCode);
            }

            $products = $collection->getItems();
            foreach ($keysToLoad as $key => $keyData) {
                $productId = $keyData['product_id'];
                if (isset($products[$productId])) {
                    $this->orderItemProducts[$key] = $products[$productId];
                }
            }
        }

        $orderItemsProducts = array_intersect_key($this->orderItemProducts, $keys);
        foreach ($orderItemsProducts as $itemsProduct) {
            $result[$itemsProduct->getId()] = $itemsProduct;
        }
        return $result;
    }

    /**
     * Get order items product Ids
     *
     * @param Item[] $orderItems
     * @return array
     */
    private function getOrderItemsProductIds(array $orderItems)
    {
        /**
         * @param Item $orderItem
         * @return int
         */
        $mapToProductIdsCallback = function ($orderItem) {
            return $orderItem->getProductId();
        };
        return array_map($mapToProductIdsCallback, $orderItems);
    }

    /**
     * Get order items products registry keys
     *
     * @param int $storeId
     * @param array $productIds
     * @return array
     */
    private function getOrderItemsProductKeys($storeId, array $productIds)
    {
        $result = [];
        foreach ($productIds as $productId) {
            $key = $productId . '-' . $storeId;
            $result[$key] = [
                'store_id' => $storeId,
                'product_id' => $productId
            ];
        }
        return $result;
    }

    /**
     * Clear order items products internal registry
     *
     * @return void
     */
    private function clearOrderItemProducts()
    {
        $this->orderItemProducts = [];
    }

    /**
     * Prepare index row
     *
     * @param array $staticFields
     * @param array $attributeCodes
     * @param Item $orderItem
     * @param Product $product
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function prepareIndexRow(
        array $staticFields,
        array $attributeCodes,
        Item $orderItem,
        Product $product
    ) {
        $row = ['order_item_id' => (int)$orderItem->getId()];

        foreach ($staticFields as $field) {
            if ($product->hasData($field)) {
                $row[$field] = $product->getData($field);
            }
        }
        foreach ($attributeCodes as $attributeCode) {
            if ($product->hasData($attributeCode)) {
                $row[$attributeCode] = $this->prepareAttributeValue($product, $attributeCode);
            }
        }

        return $row;
    }

    /**
     * Prepare attribute value
     *
     * @param Product $product
     * @param string $attributeCode
     * @return string|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function prepareAttributeValue(Product $product, $attributeCode)
    {
        $attribute = $this->eavConfig->getAttribute(Product::ENTITY, $attributeCode);
        $value = $product->getData($attributeCode);

        if ($attribute->usesSource()) {
            $options = $this->getAttributeOptions($attribute);
            if ($this->getAttributeType($attribute) == 'multiselect') {
                $optionIds = explode(',', $value);
                $valueOptions = array_intersect_key($options, array_flip($optionIds));
                $value = implode(',', $valueOptions);
            } elseif (isset($options[$value])) {
                $value = $options[$value];
            }
        }

        return $this->isValidAttributeValue($value)
            ? $value
            : null;
    }

    /**
     * Get attribute type
     *
     * @param AbstractAttribute $attribute
     * @return string
     */
    private function getAttributeType(AbstractAttribute $attribute)
    {
        $frontendInput = $attribute->getFrontendInput();
        if ($attribute->usesSource()
            && in_array($frontendInput, ['select', 'multiselect', 'boolean'])
        ) {
            return $frontendInput;
        } elseif ($attribute->isStatic()) {
            return $frontendInput == 'date'
                ? 'datetime'
                : 'varchar';
        }
        return $attribute->getBackendType();
    }

    /**
     * Get attribute options
     *
     * @param AbstractAttribute $attribute
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getAttributeOptions(AbstractAttribute $attribute)
    {
        $attributeCode = $attribute->getAttributeCode();
        if (!isset($this->attributeOptions[$attributeCode])) {
            $options = [];

            $attribute->setStoreId(Store::DEFAULT_STORE_ID);
            if ($attribute->usesSource()) {
                $index = in_array($attributeCode, self::INDEX_VALUE_ATTRIBUTES)
                    ? 'value'
                    : 'label';

                foreach ($attribute->getSource()->getAllOptions() as $option) {
                    $innerOptions = is_array($option['value'])
                        ? $option['value']
                        : [$option];
                    foreach ($innerOptions as $innerOption) {

                        // Avoid ' -- Please Select -- ' option adding
                        if (is_numeric($innerOption['value'])) {
                            $innerOption['value'] = (string)$innerOption['value'];
                        }
                        if (strlen($innerOption['value'])) {
                            $options[$innerOption['value']] = (string)$innerOption[$index];
                        }
                    }
                }
            }

            $this->attributeOptions[$attributeCode] = $options;
        }
        return $this->attributeOptions[$attributeCode];
    }

    /**
     * Checks if attribute value is valid
     *
     * @param mixed $attributeValue
     * @return bool
     */
    private function isValidAttributeValue($attributeValue)
    {
        if (!is_numeric($attributeValue) && empty($attributeValue)) {
            return false;
        }
        if (is_array($attributeValue)) {
            return false;
        }
        return true;
    }
}
