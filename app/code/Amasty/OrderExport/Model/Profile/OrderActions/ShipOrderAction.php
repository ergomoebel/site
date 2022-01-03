<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */

declare(strict_types=1);

namespace Amasty\OrderExport\Model\Profile\OrderActions;

use Amasty\OrderExport\Api\OrderActionInterface;
use Amasty\OrderExport\Model\Profile\OrderActionsDataRegistry;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Sales\Model\Convert\Order;

class ShipOrderAction implements OrderActionInterface
{
    /**
     * @var Order
     */
    private $convertOrder;

    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var NotifyCustomerShipment
     */
    private $notifyCustomerShipment;

    public function __construct(
        Order $convertOrder,
        ShipmentRepositoryInterface $shipmentRepository,
        NotifyCustomerShipment $notifyCustomerShipment
    ) {
        $this->convertOrder = $convertOrder;
        $this->shipmentRepository = $shipmentRepository;
        $this->notifyCustomerShipment = $notifyCustomerShipment;
    }

    public function execute(OrderInterface $order, array $actionData = [])
    {
        if (!$order->canShip()) {
            return;
        }
        $shipment = $this->convertOrder->toShipment($order);

        foreach ($order->getAllItems() as $orderItem) {
            if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                continue;
            }
            $shipmentItem = $this->convertOrder->itemToShipmentItem($orderItem)->setQty($orderItem->getQtyToShip());
            $shipment->addItem($shipmentItem);
        }
        $shipment->register();
        $shipment->getOrder()->setIsInProcess(true);
        $this->shipmentRepository->save($shipment);

        if ($actionData['is_notify'] ?? null) {
            $this->notifyCustomerShipment->execute($order, $shipment);
        }
    }
}
