<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Model\LastExportedId\ResourceModel;

use Amasty\ExportPro\Model\LastExportedId\LastExportedId;
use Amasty\ExportPro\Model\LastExportedId\ResourceModel\LastExportedId as LastExportedIdResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(LastExportedId::class, LastExportedIdResource::class);
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }
}
