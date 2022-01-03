<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExportEntity
 */

declare(strict_types=1);

namespace Amasty\ProductExportEntity\Model\ResourceModel\Product\Type\Bundle\Option\Value;

use Amasty\ProductExportEntity\Model\ResourceModel\Product\Type\Bundle\Option\Value as ValueResource;
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
        $this->_init(DataObject::class, ValueResource::class);
    }

    protected function _initSelect()
    {
        $connection = $this->getConnection();
        $this->getSelect()
            ->from(['main_table' => $this->getMainTable()])
            ->joinLeft(
                ['main_table_store' => $this->getMainTable()],
                $connection->quoteInto(
                    'main_table_store.option_id = main_table.option_id '
                    . 'AND main_table_store.store_id = ?',
                    $this->storeId
                ),
                ['title' => $this->getTitleExpr()]
            )->where(
                'main_table.store_id = ?',
                Store::DEFAULT_STORE_ID
            );

        $this->addFilterToMap(
            'value_id',
            'main_table.value_id'
        )->addFilterToMap(
            'option_id',
            'main_table.option_id'
        )->addFilterToMap(
            'title',
            $this->getTitleExpr()
        )->addFilterToMap(
            'parent_product_id',
            'main_table.parent_product_id'
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
     * Get title check sql expression
     *
     * @return \Zend_Db_Expr
     */
    private function getTitleExpr()
    {
        return $this->getConnection()->getCheckSql(
            'main_table_store.title IS NULL',
            'main_table.title',
            'main_table_store.title'
        );
    }
}
