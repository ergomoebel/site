<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerImportEntity
 */


declare(strict_types=1);

namespace Amasty\CustomerImportEntity\Import\Validation\RelationValidator;

use Amasty\ImportCore\Api\Validation\RelationValidatorInterface;

class CustomerAddressValidator implements RelationValidatorInterface
{
    /**
     * @var string|null
     */
    private $message;

    public function validate(array $entityRow, array $subEntityRows): bool
    {
        foreach ($subEntityRows as $subEntityRow) {
            if (!$this->checkParentId($entityRow, $subEntityRow)) {
                $this->message = __(
                    'Wrong parent_id specified for customer address entity %1',
                    $subEntityRow['entity_id'] ?? ''
                )->render();

                return false;
            }
        }

        return true;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    private function checkParentId(array $entityRow, array $subEntityRow): bool
    {
        if (!isset($subEntityRow['parent_id'])
            || !isset($entityRow['entity_id'])
            || $entityRow['entity_id'] != $subEntityRow['parent_id']
        ) {
            return false;
        }

        return true;
    }
}
