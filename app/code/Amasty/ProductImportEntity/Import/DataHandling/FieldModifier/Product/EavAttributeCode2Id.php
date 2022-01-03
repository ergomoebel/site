<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\DataHandling\FieldModifier\Product;

use Amasty\ImportCore\Api\Modifier\FieldModifierInterface;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\ResourceModel\Entity\Attribute as EavResource;

class EavAttributeCode2Id implements FieldModifierInterface
{
    /**
     * @var EavResource
     */
    private $eavResource;

    public function __construct(EavResource $eavResource)
    {
        $this->eavResource = $eavResource;
    }

    /**
     * @inheritDoc
     */
    public function transform($value)
    {
        $attributeId = $this->eavResource->getIdByCode(Product::ENTITY, trim($value));

        return $attributeId ?: $value;
    }
}
