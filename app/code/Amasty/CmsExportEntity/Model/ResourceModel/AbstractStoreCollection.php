<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CmsExportEntity
 */

declare(strict_types=1);

namespace Amasty\CmsExportEntity\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

abstract class AbstractStoreCollection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'store_id') {
            return $this->addStoreFilter($condition);
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add store filter
     *
     * @param array|string|null $condition
     * @return $this
     */
    public function addStoreFilter($condition)
    {
        $storeIds = $this->getConditionIds($condition);

        $isNegation = $this->isNegation($condition);
        $isAllStoreFilter = in_array('0', $storeIds);
        if ($isAllStoreFilter && !$isNegation || !count($storeIds)) {
            return $this;
        }
        if (!$isAllStoreFilter) {
            $storeIds[] = 0;
        }

        $select = $this->getSelect();
        if ($isNegation) {
            if ($isAllStoreFilter) {
                $select->where(new \Zend_Db_Expr(0));
            } else {
                $select->where('store_id NOT IN (?)', $storeIds);
            }
        } else {
            $select->where('store_id IN (?)', $storeIds);
        }

        return $this;
    }

    /**
     * Get ids array from filter condition
     *
     * @param array|string|null $condition
     * @return array
     */
    private function getConditionIds($condition)
    {
        if (is_array($condition)) {
            if (array_key_exists('in', $condition)) {
                return $condition['in'];
            } elseif (array_key_exists('nin', $condition)) {
                return $condition['nin'];
            }

            return [current($condition)];
        } elseif ($condition !== null) {
            return [$condition];
        }

        return [];
    }

    /**
     * Checks if specified condition contains negation
     *
     * @param array|string|null $condition
     * @return bool
     */
    private function isNegation($condition)
    {
        if (is_array($condition)) {
            $keys = array_keys($condition);
            if (count($keys)) {
                return in_array($keys[0], ['nin', 'neq']);
            }
        }

        return false;
    }
}
