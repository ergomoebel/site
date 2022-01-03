<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExportEntity
 */

declare(strict_types=1);

namespace Amasty\OrderExportEntity\Model;

class Attribute extends \Magento\Framework\Model\AbstractModel
{
    const ATTRIBUTE_ID = 'entity_id';

    protected function _construct()
    {
        $this->_init(\Amasty\OrderExportEntity\Model\ResourceModel\Attribute::class);
    }
}
