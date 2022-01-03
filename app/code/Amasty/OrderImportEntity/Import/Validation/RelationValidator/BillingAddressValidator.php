<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderImportEntity
 */

declare(strict_types=1);

namespace Amasty\OrderImportEntity\Import\Validation\RelationValidator;

use Amasty\ImportCore\Api\Validation\RelationValidatorInterface;

class BillingAddressValidator implements RelationValidatorInterface
{
    /**
     * @var string|null
     */
    private $message;

    public function validate(array $entityRow, array $subEntityRows): bool
    {
        if (isset($entityRow['billing_address_id']) && empty($subEntityRows)) {
            $this->message = __(
                'No billing address provided for order %1',
                $entityRow['entity_id']
            )->render();

            return false;
        }

        foreach ($subEntityRows as $subEntityRow) {
            if (!$this->checkAddressId($entityRow, $subEntityRow)) {
                $this->message = __(
                    'Wrong billing address specified for order %1',
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

    private function checkAddressId(array $entityRow, array $subEntityRow): bool
    {
        if (!isset($subEntityRow['entity_id'])
            || !isset($entityRow['billing_address_id'])
            || $entityRow['billing_address_id'] != $subEntityRow['entity_id']
        ) {
            return false;
        }

        return true;
    }
}
