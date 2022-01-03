<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExportEntity
 */

declare(strict_types=1);

namespace Amasty\OrderExportEntity\Model\ResourceModel;

use Amasty\OrderExportEntity\Model\Attribute as AttributeModel;

class Attribute extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const TABLE_NAME = 'amasty_order_export_attribute';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, AttributeModel::ATTRIBUTE_ID);
    }
}
