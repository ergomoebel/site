<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\Behavior\Product\Save\Link;

use Magento\Catalog\Model\Product\Link;

class CrossSellLink extends AbstractLink
{
    /**
     * @inheritDoc
     */
    protected function getLinkTypeId()
    {
        return Link::LINK_TYPE_CROSSSELL;
    }
}
