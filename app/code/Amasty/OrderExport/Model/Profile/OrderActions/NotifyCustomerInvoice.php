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
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;

class NotifyCustomerInvoice
{
    /**
     * @var InvoiceSender
     */
    private $invoiceSender;

    /**
     * @var State
     */
    private $appState;

    public function __construct(
        InvoiceSender $invoiceSender,
        State $appState
    ) {
        $this->invoiceSender = $invoiceSender;
        $this->appState = $appState;
    }

    public function execute(OrderInterface $order, InvoiceInterface $invoice)
    {
        if ((int)$invoice->getOrderId() !== (int)$order->getId()) {
            return;
        }
        //need to emulate frontend area because of theme full path problem
        // in \Magento\Framework\View\Design\Fallback\Rule\Theme::getPatternDirs
        $this->appState->emulateAreaCode(Area::AREA_FRONTEND, [$this->invoiceSender, 'send'], [$invoice]);
        $order->addCommentToStatusHistory(
            __('The customer was notified about the invoice creation.')
        )->setIsCustomerNotified(true);
    }
}
