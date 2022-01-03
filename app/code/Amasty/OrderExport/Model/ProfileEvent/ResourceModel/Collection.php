<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


declare(strict_types=1);

namespace Amasty\OrderExport\Model\ProfileEvent\ResourceModel;

use Amasty\OrderExport\Model\ProfileEvent\ProfileEvent as ProfileEventModel;
use Amasty\OrderExport\Model\ProfileEvent\ResourceModel\ProfileEvent as ProfileEventResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(ProfileEventModel::class, ProfileEventResource::class);
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }
}
