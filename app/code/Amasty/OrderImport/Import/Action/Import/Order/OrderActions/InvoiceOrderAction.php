<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderImport
 */

declare(strict_types=1);

namespace Amasty\OrderImport\Import\Action\Import\Order\OrderActions;

use Amasty\OrderImport\Api\OrderActionInterface;
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

    public function execute(OrderInterface $order, array $actionData = []): void
    {
        if (!$order->canInvoice()
            || ($actionData['invoice_order_new'] && !$actionData['isNew'])
        ) {
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

        if ($actionData['notify_customer_invoice'] ?? null) {
            $this->notifyCustomerInvoice->execute($order, $invoice);
        }
    }
}
