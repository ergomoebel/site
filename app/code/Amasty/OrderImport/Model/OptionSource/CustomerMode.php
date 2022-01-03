<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderImport
 */

declare(strict_types=1);

namespace Amasty\OrderImport\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class CustomerMode implements OptionSourceInterface
{
    const NO_CUSTOMER_GUEST = 1;
    const ALL_ORDERS_GUEST = 2;
    const CREATE_CUSTOMER = 3;

    public function toOptionArray(): array
    {
        return [
            ['value' => self::NO_CUSTOMER_GUEST, 'label' => __('Guest Order If Customer Doesn\'t Exist')],
            ['value' => self::ALL_ORDERS_GUEST, 'label' => __('All Orders as Guest Orders')],
            ['value' => self::CREATE_CUSTOMER, 'label' => __('Create Customer If Customer Doesn\'t Exist')]
        ];
    }
}
