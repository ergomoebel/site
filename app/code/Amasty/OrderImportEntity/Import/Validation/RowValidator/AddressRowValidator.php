<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderImportEntity
 */

declare(strict_types=1);

namespace Amasty\OrderImportEntity\Import\Validation\RowValidator;

use Amasty\ImportCore\Api\Validation\RowValidatorInterface;

abstract class AddressRowValidator implements RowValidatorInterface
{
    /**
     * @var string|null
     */
    private $message;

    abstract protected function getAddressType(): string;

    public function validate(array $row): bool
    {
        $this->message = null;

        if (!$this->validateAddressType($row)) {
            $this->message = __('Invalid address type specified.')->getText();

            return false;
        }

        if (!$this->isEmailValid($row)) {
            $this->message = __('Email has a wrong format.')->getText();

            return false;
        }

        return true;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    private function validateAddressType(array $row): bool
    {
        if (isset($row['address_type']) && $row['address_type'] !== $this->getAddressType()) {
            return false;
        }

        return true;
    }

    private function isEmailValid(array $row): bool
    {
        if (!isset($row['email']) || !filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return true;
    }
}
