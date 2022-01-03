<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_InventoryExportEntity
 */

declare(strict_types=1);

namespace Amasty\InventoryExportEntity\Model\ResourceModel\Reservation;

use Amasty\InventoryExportEntity\Model\ResourceModel\Reservation as ReservationResource;
use Magento\Framework\DataObject;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(DataObject::class, ReservationResource::class);
    }
}
