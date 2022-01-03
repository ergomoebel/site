<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderImportEntity
 */

declare(strict_types=1);

namespace Amasty\OrderImportEntity\Import\Validation\RowValidator;

class ShippingAddressRowValidator extends AddressRowValidator
{
    protected function getAddressType(): string
    {
        return 'shipping';
    }
}
