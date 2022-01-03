<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExportEntity
 */

declare(strict_types=1);

namespace Amasty\ProductExportEntity\Model\ResourceModel\ScopedEntity;

use Magento\Framework\Data\Collection\AbstractDb;

interface ItemCollectorInterface
{
    const STORE_ID_FIELD = 'store_id';

    /**
     * Set fields to select
     *
     * @param array $fieldsToSelect
     * @return $this
     */
    public function setFieldsToSelect(array $fieldsToSelect);

    /**
     * Set fields to filter
     *
     * @param array $fieldsToFilter
     * @return $this
     */
    public function setFieldsToFilter(array $fieldsToFilter);

    /**
     * Collect scoped entities
     *
     * @param AbstractDb $collection
     * @param array $defaultItems
     * @param int|int[] $scope
     * @return array
     */
    public function collect(AbstractDb $collection, array $defaultItems, $scope);
}
