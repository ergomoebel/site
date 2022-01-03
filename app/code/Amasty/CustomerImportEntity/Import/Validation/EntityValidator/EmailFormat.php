<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerImportEntity
 */

declare(strict_types=1);

namespace Amasty\CustomerImportEntity\Import\Validation\EntityValidator;

use Amasty\ImportCore\Api\Validation\FieldValidatorInterface;

class EmailFormat implements FieldValidatorInterface
{
    public function validate(array $row, string $field): bool
    {
        if (!isset($row[$field]) || !filter_var($row[$field], FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return true;
    }
}
