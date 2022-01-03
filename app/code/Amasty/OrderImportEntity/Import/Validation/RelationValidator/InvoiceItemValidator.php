<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderImportEntity
 */

declare(strict_types=1);

namespace Amasty\OrderImportEntity\Import\Validation\RelationValidator;

use Amasty\ImportCore\Api\Validation\RelationValidatorInterface;

class InvoiceItemValidator implements RelationValidatorInterface
{
    /**
     * @var string|null
     */
    private $message;

    public function validate(array $entityRow, array $subEntityRows): bool
    {
        if (!$this->validateItemsQty($entityRow, $subEntityRows)) {
            $this->message = __(
                'Wrong invoice_item qty specified for invoice %1',
                $entityRow['entity_id'] ?? ''
            )->render();

            return false;
        }

        return true;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    private function validateItemsQty(array $entityRow, array $subEntityRows): bool
    {
        if (!isset($entityRow['total_qty'])) {
            return true;
        }
        $itemsQty = .0;

        foreach ($subEntityRows as $row) {
            $itemsQty += (float)($row['qty'] ?? 0);
        }

        return $itemsQty === (float)$entityRow['total_qty'];
    }
}
