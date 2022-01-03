<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderImportEntity
 */

declare(strict_types=1);

namespace Amasty\OrderImportEntity\Import\Validation\RelationValidator;

use Amasty\ImportCore\Api\Validation\RelationValidatorInterface;

class OrderItemValidator implements RelationValidatorInterface
{
    /**
     * @var string|null
     */
    private $message;

    public function validate(array $entityRow, array $subEntityRows): bool
    {
        foreach ($subEntityRows as $subEntityRow) {
            if (!$this->validateVirtualOrder($entityRow, $subEntityRow)) {
                $this->message = __(
                    'Non virtual order_item %1 found for virtual order %2',
                    $subEntityRow['order_id'] ?? '',
                    $entityRow['entity_id'] ?? ''
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

    private function validateVirtualOrder(array $entityRow, array $subEntity): bool
    {
        if (isset($entityRow['is_virtual']) && $entityRow['is_virtual'] == 1
            && isset($subEntity['is_virtual']) && $subEntity['is_virtual'] == 0
        ) {
            return false;
        }

        return true;
    }
}
