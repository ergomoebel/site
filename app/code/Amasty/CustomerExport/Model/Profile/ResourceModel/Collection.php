<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerExport
 */


declare(strict_types=1);

namespace Amasty\CustomerExport\Model\Profile\ResourceModel;

use Amasty\CustomerExport\Model\Profile\Profile as ProfileModel;
use Amasty\CustomerExport\Model\Profile\ResourceModel\Profile as ProfileResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(ProfileModel::class, ProfileResource::class);
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }

    /**
     * Serializable fields load fix
     *
     * @return Collection
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();

        foreach ($this->getItems() as $item) {
            $this->getResource()->unserializeFields($item);
            $item->setDataChanges(false);
        }

        return $this;
    }
}
