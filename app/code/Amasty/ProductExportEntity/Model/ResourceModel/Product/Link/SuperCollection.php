<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExportEntity
 */

declare(strict_types=1);

namespace Amasty\ProductExportEntity\Model\ResourceModel\Product\Link;

use Magento\GroupedProduct\Model\ResourceModel\Product\Link;

class SuperCollection extends AbstractCollection
{
    protected function getLinkTypeId()
    {
        return Link::LINK_TYPE_GROUPED;
    }
}
