<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\DataHandling\RowModifier\Product\Link;

use Amasty\ImportCore\Api\Modifier\RowModifierInterface;
use Amasty\ProductImportEntity\Import\DataHandling\SkuToProductId;

class RelatedLink implements RowModifierInterface
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
    public function transform(array &$row): array
    {
        return $this->skuToProductId->executeRow($row, 'linked_product_id');
    }
}
