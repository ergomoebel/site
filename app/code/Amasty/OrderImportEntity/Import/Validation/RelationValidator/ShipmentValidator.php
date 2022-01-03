<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderImportEntity
 */

declare(strict_types=1);

namespace Amasty\OrderImportEntity\Import\Validation\RelationValidator;

use Amasty\ImportCore\Api\Validation\RelationValidatorInterface;

class ShipmentValidator implements RelationValidatorInterface
{
    /**
     * @var string|null
     */
    private $message;

    public function validate(array $entityRow, array $subEntityRows): bool
    {
        foreach ($subEntityRows as $subEntityRow) {
            if (!$this->isStoreCorrect($entityRow, $subEntityRow)) {
                $this->message = __(
                    'store_id of order %1 doesn\'t match store_id of shipment %2',
                    $entityRow['entity_id'] ?? '',
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

    private function isStoreCorrect(array $entityRow, array $subEntityRow): bool
    {
        if (isset($entityRow['store_id'])
            && isset($subEntityRow['store_id'])
            && $entityRow['store_id'] != $subEntityRow['store_id']
        ) {
            return false;
        }

        return true;
    }
}
