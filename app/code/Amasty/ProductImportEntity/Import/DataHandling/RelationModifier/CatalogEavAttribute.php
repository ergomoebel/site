<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\DataHandling\RelationModifier;

use Amasty\ImportCore\Import\DataHandling\RelationModifier\EavAttribute;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as CatalogAttribute;

class CatalogEavAttribute extends EavAttribute
{
    /**
     * @inheritDoc
     */
    protected function isGlobalScope(AttributeInterface $attribute): bool
    {
        if ($attribute instanceof CatalogAttribute && $attribute->isScopeGlobal()) {
            return true;
        }

        return parent::isGlobalScope($attribute);
    }
}
