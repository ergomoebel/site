<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExportEntity
 */

declare(strict_types=1);

namespace Amasty\ProductExportEntity\Model\ResourceModel\ScopedEntity\Collection\Field;

class ArgumentProcessor
{
    /**
     * Checks if field definition contains specified field name
     *
     * @param string $fieldName
     * @param mixed $fieldDef
     * @return bool
     */
    public function hasField($fieldName, $fieldDef)
    {
        return is_array($fieldDef) && in_array($fieldName, $fieldDef)
            || $fieldName == $fieldDef;
    }

    /**
     * Exclude field name from field definition
     *
     * @param string $fieldName
     * @param mixed $fieldDef
     * @return mixed|null
     */
    public function excludeField($fieldName, $fieldDef)
    {
        if (is_array($fieldDef)) {
            $key = array_search($fieldName, $fieldDef);
            if ($key !== false) {
                unset($fieldDef[$key]);
            }
        } elseif ($fieldDef == $fieldName) {
            return null;
        }

        return $fieldDef;
    }

    /**
     * Get field names array from field definition
     *
     * @param mixed $fieldDef
     * @return array
     */
    public function getFieldNames($fieldDef)
    {
        return is_array($fieldDef[0])
            ? $fieldDef[0]
            : [$fieldDef[0]];
    }
}
