<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExportEntity
 */

declare(strict_types=1);

namespace Amasty\ProductExportEntity\Model\ResourceModel\Product\Review;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Review\Model\ResourceModel\Review\Collection as ReviewCollection;
use Magento\Review\Model\Review;

class Collection extends ReviewCollection
{
    const PRODUCT_REVIEW_FIELDS = [
        'entity_id',
        'created_at',
        'status_id'
    ];

    const REVIEW_DETAIL_FIELDS = [
        'store_id',
        'review_id',
        'detail_id',
        'title',
        'detail',
        'nickname',
        'customer_id'
    ];

    const CUSTOMER_ENTITY_FIELDS = [
        CustomerInterface::EMAIL,
        CustomerInterface::FIRSTNAME,
        CustomerInterface::LASTNAME
    ];

    const PRODUCT_ENTITY_FIELDS = ['product_sku' => 'product_entity_table.sku'];

    /**
     * @inheritdoc
     */
    protected function _initSelect()
    {
        AbstractCollection::_initSelect();
        $this->getSelect()->join(
            ['detail' => $this->getReviewDetailTable()],
            'main_table.review_id = detail.review_id',
            [
                'customer_id',
                'store_id',
                'detail_id',
                'title',
                'detail',
                'nickname'
            ]
        );
        $this->getSelect()
            ->join(
                ['review_entity' => $this->getReviewEntityTable()],
                'main_table.entity_id = review_entity.entity_id',
                ''
            )
            ->where('review_entity.entity_code = ?', Review::ENTITY_PRODUCT_CODE);

        $map = [
            'main_table' => self::PRODUCT_REVIEW_FIELDS,
            'detail' => self::REVIEW_DETAIL_FIELDS
        ];
        foreach ($map as $table => $fields) {
            foreach ($fields as $field) {
                $this->addFilterToMap(
                    $field,
                    $table . '.' . $field
                );
            }
        }

        return $this;
    }

    private function isReviewDetailField($field): bool
    {
        return in_array($field, self::REVIEW_DETAIL_FIELDS);
    }

    private function isCustomerEntityField($field): bool
    {
        return in_array($field, self::CUSTOMER_ENTITY_FIELDS);
    }

    private function isProductEntityField($fieldName): bool
    {
        $productEntityFields = array_keys(self::PRODUCT_ENTITY_FIELDS);

        return in_array($fieldName, $productEntityFields);
    }

    /**
     * Join customer entity table
     *
     * @return $this
     */
    private function joinCustomerEntityTable()
    {
        if (!$this->getFlag('customer_entity_table_joined')) {
            $this->getSelect()->joinLeft(
                ['customer_entity_table' => $this->getTable('customer_entity')],
                'customer_entity_table.entity_id = detail.customer_id',
                self::CUSTOMER_ENTITY_FIELDS
            );
            $this->setFlag('customer_entity_table_joined', true);
        }
        return $this;
    }

    /**
     * Join product entity table
     *
     * @return $this
     */
    private function joinProductEntityTable()
    {
        if (!$this->getFlag('product_entity_table_joined')) {
            $this->getSelect()->joinLeft(
                ['product_entity_table' => $this->getTable('catalog_product_entity')],
                'product_entity_table.entity_id = main_table.entity_pk_value',
                self::PRODUCT_ENTITY_FIELDS
            );
            $this->setFlag('product_entity_table_joined', true);
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addFieldToSelect($field, $alias = null)
    {
        if (!is_array($field)) {
            if ($this->isReviewDetailField($field)) {
                return $this;
            } elseif ($this->isCustomerEntityField($field)) {
                return $this->joinCustomerEntityTable($field);
            } elseif ($this->isProductEntityField($field)) {
                return $this->joinProductEntityTable($field);
            }
        }

        return parent::addFieldToSelect($field, $alias);
    }
}
