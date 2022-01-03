<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\DataHandling\FieldModifier\Product;

use Amasty\ImportCore\Api\Modifier\FieldModifierInterface;
use Amasty\ProductImportEntity\Import\DataHandling\SkuToProductId;

class Sku2ProductId implements FieldModifierInterface
{
    /**
     * @var SkuToProductId
     */
    private $skuToProductId;

    public function __construct(SkuToProductId $skuToProductId)
    {
        $this->skuToProductId = $skuToProductId;
    }

    /**
     * @inheritDoc
     */
    public function transform($value)
    {
        $productId = $this->skuToProductId->executeValue($value);
        if ($productId) {
            return $productId;
        }

        return null;
    }
}
