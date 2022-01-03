<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerExportEntity
 */


declare(strict_types=1);

namespace Amasty\CustomerExportEntity\Model\ResourceModel\Address;

use Magento\Customer\Model\ResourceModel\Address\Collection as AddressCollection;
use Magento\Framework\Data\Collection\AbstractDb;

class Collection extends AddressCollection
{
    /**
     * Wrapper for compatibility with \Magento\Framework\Data\Collection\AbstractDb
     * Fixed filtering by the field "is_active"
     *
     * @param mixed $attribute
     * @param mixed $condition
     * @return $this|AbstractDb
     * @codeCoverageIgnore
     */
    public function addFieldToFilter($attribute, $condition = null)
    {
        if ($attribute == 'is_active') {
            return AbstractDb::addFieldToFilter($attribute, $condition);
        } else {
            return parent::addFieldToFilter($attribute, $condition);
        }
    }
}
