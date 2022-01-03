<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExportEntity
 */

declare(strict_types=1);

namespace Amasty\ProductExportEntity\Model\ResourceModel\Product\Link;

use Magento\Catalog\Model\Product\Link;

class RelatedCollection extends AbstractCollection
{
    protected function getLinkTypeId()
    {
        return Link::LINK_TYPE_RELATED;
    }
}
