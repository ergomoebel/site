<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderImport
 */

declare(strict_types=1);

namespace Amasty\OrderImport\Import\Action\Import\Customer;

use Amasty\OrderImport\Model\OptionSource\CustomerMode;

class CustomerModeStrategyResolver
{
    /**
     * @var CustomerStrategyFactory
     */
    private $customerStrategyFactory;

    public function __construct(
        CustomerStrategyFactory $customerStrategyFactory
    ) {
        $this->customerStrategyFactory = $customerStrategyFactory;
    }

    public function resolveStrategy(int $customerModeType): ?CustomerStrategy
    {
        switch ($customerModeType) {
            case CustomerMode::NO_CUSTOMER_GUEST:
                return $this->customerStrategyFactory->create(['strategyToPerform' => 'guestOrderIfNoCustomer']);
            case CustomerMode::ALL_ORDERS_GUEST:
                return $this->customerStrategyFactory->create(['strategyToPerform' => 'assignGuestToOrder']);
            case CustomerMode::CREATE_CUSTOMER:
                return $this->customerStrategyFactory->create(['strategyToPerform' => 'createCustomerIfNoCustomer']);
            default:
                return null;
        }
    }
}
