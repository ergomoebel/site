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
use Magento\Framework\DB\Transaction;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Service\InvoiceService;

class InvoiceOrderAction implements OrderActionInterface
{
    /**
     * @var InvoiceService
     */
    private $invoiceService;

    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * @var NotifyCustomerInvoice
     */
    private $notifyCustomerInvoice;

    public function __construct(
        InvoiceService $invoiceService,
        Transaction $transaction,
        NotifyCustomerInvoice $notifyCustomerInvoice
    ) {
        $this->invoiceService = $invoiceService;
        $this->transaction = $transaction;
        $this->notifyCustomerInvoice = $notifyCustomerInvoice;
    }

    public function execute(OrderInterface $order, array $actionData = [])
    {
        if (!$order->canInvoice()) {
            return;
        }
        $invoice = $this->invoiceService->prepareInvoice($order);

        if (!$invoice->getTotalQty()) {
            return;
        }
        $invoice->register();
        $invoice->getOrder()->setCustomerNoteNotify(false);
        $invoice->getOrder()->setIsInProcess(true);
        $transactionSave = $this->transaction->addObject(
            $invoice
        )->addObject(
            $invoice->getOrder()
        );
        $transactionSave->save();

        if ($actionData['is_notify'] ?? null) {
            $this->notifyCustomerInvoice->execute($order, $invoice);
        }
    }
}
