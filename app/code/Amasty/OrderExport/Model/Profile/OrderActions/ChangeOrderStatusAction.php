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
use Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory;

class ChangeOrderStatusAction implements OrderActionInterface
{
    public function execute(OrderInterface $order, array $actionData = [])
    {
        if ($value = $actionData['value'] ?? null) {
            $order->setStatus($value);
        }
    }
}
