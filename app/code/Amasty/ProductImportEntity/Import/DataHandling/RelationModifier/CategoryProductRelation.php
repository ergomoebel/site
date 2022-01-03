<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\DataHandling\RelationModifier;

use Amasty\ImportCore\Api\Modifier\RelationModifierInterface;

class CategoryProductRelation implements RelationModifierInterface
{
    /**
     * @inheritDoc
     */
    public function transform(array &$entityRow, array &$subEntityRows): array
    {
        if (isset($entityRow['sku'])) {
            foreach ($subEntityRows as &$categoryRow) {
                $categoryRow['product_sku'] = $entityRow['sku'];
            }
        }

        return $entityRow;
    }
}
