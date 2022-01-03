<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderImport
 */

declare(strict_types=1);

namespace Amasty\OrderImport\Api;

use Magento\Sales\Api\Data\OrderInterface;

interface OrderActionInterface
{
    public function execute(OrderInterface $order, array $actionData = []): void;
}
