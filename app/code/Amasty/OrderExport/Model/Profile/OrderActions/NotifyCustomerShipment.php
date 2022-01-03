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
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Model\Order\Email\Sender\ShipmentSender;

class NotifyCustomerShipment
{
    /**
     * @var ShipmentSender
     */
    private $shipmentSender;

    /**
     * @var State
     */
    private $appState;

    public function __construct(
        ShipmentSender $shipmentSender,
        State $appState
    ) {
        $this->shipmentSender = $shipmentSender;
        $this->appState = $appState;
    }

    public function execute(OrderInterface $order, ShipmentInterface $shipment)
    {
        if ((int)$shipment->getOrderId() !== (int)$order->getId()) {
            return;
        }
        //need to emulate frontend area because of theme full path problem
        // in \Magento\Framework\View\Design\Fallback\Rule\Theme::getPatternDirs
        $this->appState->emulateAreaCode(Area::AREA_FRONTEND, [$this->shipmentSender, 'send'], [$shipment]);
        $order->addCommentToStatusHistory(
            __('The customer was notified about the shipment creation.')
        )->setIsCustomerNotified(true);
    }
}
