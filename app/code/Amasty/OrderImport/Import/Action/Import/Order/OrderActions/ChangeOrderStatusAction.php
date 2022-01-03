<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderImport
 */

declare(strict_types=1);

namespace Amasty\OrderImport\Import\Action\Import\Order\OrderActions;

use Amasty\OrderImport\Api\OrderActionInterface;
use Magento\Sales\Api\Data\OrderInterface;

class ChangeOrderStatusAction implements OrderActionInterface
{
    public function execute(OrderInterface $order, array $actionData = []): void
    {
        if ($actionData['change_status_new'] && !$actionData['isNew'] ?? '') {
            return;
        }

        if ($value = $actionData['value'] ?? null) {
            $order->setStatus($value);
        }
    }
}
