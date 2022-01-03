<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\DataHandling\FieldModifier\Product\Bundle;

use Amasty\ImportCore\Api\Modifier\FieldModifierInterface;

class TypeValue2TypeLabel implements FieldModifierInterface
{
    const TYPE_DYNAMIC = 0;
    const TYPE_FIXED = 1;

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
            'dynamic' => self::TYPE_DYNAMIC,
            'fixed' => self::TYPE_FIXED,
        ];
    }
}
