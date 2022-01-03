<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Model\History\ResourceModel;

use Amasty\ExportPro\Model\History\History;
use Amasty\ExportPro\Model\History\ResourceModel\History as HistoryResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(History::class, HistoryResource::class);
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }
}
