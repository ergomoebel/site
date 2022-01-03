<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExportEntity
 */

declare(strict_types=1);

namespace Amasty\ProductExportEntity\Model\ResourceModel\Product\TierPrice;

use Amasty\ProductExportEntity\Model\ResourceModel\Product\TierPrice as TierPriceResource;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(DataObject::class, TierPriceResource::class);
    }

    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'website_id') {
            return $this->addWebsiteFilter($condition);
        } elseif ($field == 'customer_group_id') {
            return $this->addCustomerGroupFilter($condition);
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add website filter
     *
     * @param array|string|null $condition
     * @return $this
     */
    public function addWebsiteFilter($condition)
    {
        $websiteIds = $this->getConditionIds($condition);
        if (!in_array(0, $websiteIds)) {
            $websiteIds[] = 0;
        }
        if (!count($websiteIds)) {
            return $this;
        }

        $select = $this->getSelect();
        if ($this->isNegation($condition)) {
            $select->where('website_id NOT IN (?)', $websiteIds);
        } else {
            $select->where('website_id IN (?)', $websiteIds);
        }

        return $this;
    }

    /**
     * Add customer group filter
     *
     * @param array|string|null $condition
     * @return $this
     */
    public function addCustomerGroupFilter($condition)
    {
        $connection = $this->getConnection();

        $groupIds = $this->getConditionIds($condition);
        if (!count($groupIds)) {
            return $this;
        }

        $whereConditions = [
            $connection->quoteInto('all_groups = ?', 1)
        ];

        if ($this->isNegation($condition)) {
            $whereConditions[] = $connection->quoteInto(
                'customer_group_id NOT IN (?)',
                $groupIds
            );
        } else {
            $whereConditions[] = $connection->quoteInto(
                'customer_group_id IN (?)',
                $groupIds
            );
        }

        $this->getSelect()
            ->where(implode(' OR ', $whereConditions));

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
            return array_key_exists('in', $condition)
                ? $condition['in']
                : [current($condition)];
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

    protected function _afterLoadData()
    {
        foreach ($this->_data as &$item) {
            if (isset($item['all_groups']) && $item['all_groups'] == 1) {
                $item['customer_group_id'] = GroupInterface::CUST_GROUP_ALL;
            }
        }
        return parent::_afterLoadData();
    }
}
