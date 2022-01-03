<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerExport
 */


declare(strict_types=1);

namespace Amasty\CustomerExport\Model\ProfileEvent\ResourceModel;

use Amasty\CustomerExport\Model\ProfileEvent\ProfileEvent as ProfileEventModel;
use Amasty\CustomerExport\Model\ProfileEvent\ResourceModel\ProfileEvent as ProfileEventResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(ProfileEventModel::class, ProfileEventResource::class);
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }
}
