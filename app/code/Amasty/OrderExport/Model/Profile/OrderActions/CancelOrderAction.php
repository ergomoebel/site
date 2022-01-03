<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */

declare(strict_types=1);

namespace Amasty\OrderExport\Model\Profile\OrderActions;

use Amasty\OrderExport\Api\OrderActionInterface;
use Magento\Sales\Api\Data\OrderInterface;

class CancelOrderAction implements OrderActionInterface
{
    public function execute(OrderInterface $order, array $actionData = [])
    {
        $order->cancel();
    }
}
