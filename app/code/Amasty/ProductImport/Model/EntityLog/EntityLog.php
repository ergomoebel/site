<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImport
 */


declare(strict_types=1);

namespace Amasty\ProductImport\Model\EntityLog;

use Magento\Framework\Model\AbstractModel;

class EntityLog extends AbstractModel
{
    const ID = 'id';
    const ENTITY_ID = 'entity_id';
    const PROCESS_IDENTITY = 'process_identity';
    const CREATED_AT = 'created_at';

    public function _construct()
    {
        parent::_construct();
        $this->_init(ResourceModel\EntityLog::class);
        $this->setIdFieldName(self::ID);
    }
}
