<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


declare(strict_types=1);

namespace Amasty\OrderExport\Model\Connection\ResourceModel;

use Amasty\OrderExport\Model\Connection\Connection as ConnectionModel;
use Amasty\OrderExport\Model\Connection\ResourceModel\Connection as ConnectionResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(ConnectionModel::class, ConnectionResource::class);
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }
}
