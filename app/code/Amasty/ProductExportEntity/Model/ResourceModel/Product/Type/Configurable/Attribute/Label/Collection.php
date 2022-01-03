<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExportEntity
 */

declare(strict_types=1);

namespace Amasty\ProductExportEntity\Model\ResourceModel\Product\Type\Configurable\Attribute\Label;

use Amasty\ProductExportEntity\Model\ResourceModel\Product\Type\Configurable\Attribute\Label as LabelResource;
use Magento\Framework\DataObject;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Store\Model\Store;

class Collection extends AbstractCollection
{
    /**
     * @var int
     */
    private $storeId = Store::DEFAULT_STORE_ID;

    protected function _construct()
    {
        $this->_init(DataObject::class, LabelResource::class);
    }

    protected function _initSelect()
    {
        $connection = $this->getConnection();
        $this->getSelect()
            ->from(['main_table' => $this->getMainTable()])
            ->joinLeft(
                ['main_table_store' => $this->getMainTable()],
                $connection->quoteInto(
                    'main_table_store.product_super_attribute_id = main_table.product_super_attribute_id '
                    . 'AND main_table_store.store_id = ?',
                    $this->storeId
                ),
                [
                    'value' => $this->getValueExpr(),
                    'use_default' => $this->getUseDefaultExpr()
                ]
            )->where(
                'main_table.store_id = ?',
                Store::DEFAULT_STORE_ID
            );

        $this->addFilterToMap(
            'value_id',
            'main_table.value_id'
        )->addFilterToMap(
            'product_super_attribute_id',
            'main_table.product_super_attribute_id'
        )->addFilterToMap(
            'value',
            $this->getValueExpr()
        )->addFilterToMap(
            'use_default',
            $this->getUseDefaultExpr()
        );

        return $this;
    }

    /**
     * Set store Id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
        $this->_reset();
        return $this;
    }

    /**
     * Get value check sql expression
     *
     * @return \Zend_Db_Expr
     */
    private function getValueExpr()
    {
        return $this->getConnection()->getCheckSql(
            'main_table_store.value IS NULL',
            'main_table.value',
            'main_table_store.value'
        );
    }

    /**
     * Get use default check sql expression
     *
     * @return \Zend_Db_Expr
     */
    private function getUseDefaultExpr()
    {
        return $this->getConnection()->getCheckSql(
            'main_table_store.use_default IS NULL',
            'main_table.use_default',
            'main_table_store.use_default'
        );
    }
}
