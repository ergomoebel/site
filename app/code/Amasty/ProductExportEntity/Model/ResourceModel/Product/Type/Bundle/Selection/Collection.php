<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExportEntity
 */

declare(strict_types=1);

namespace Amasty\ProductExportEntity\Model\ResourceModel\Product\Type\Bundle\Selection;

use Magento\Bundle\Model\ResourceModel\Selection as SelectionResource;
use Magento\Bundle\Model\Selection;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Selection::class, SelectionResource::class);
    }
}
