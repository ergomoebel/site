<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\DataHandling\FieldModifier\Product\Bundle;

use Amasty\ImportCore\Api\Modifier\FieldModifierInterface;

class PriceTypeLabel2PriceTypeValue implements FieldModifierInterface
{
    const PRICE_TYPE_FIXED = 0;
    const PRICE_TYPE_PERCENT = 1;

    /**
     * @inheritDoc
     */
    public function transform($value)
    {
        if (!is_array($value)) {
            $map = $this->getMap();
            return $map[$value] ?? $value;
        }

        return $value;
    }

    private function getMap(): array
    {
        return [
            'fixed' => self::PRICE_TYPE_FIXED,
            'percent' => self::PRICE_TYPE_PERCENT,
        ];
    }
}
