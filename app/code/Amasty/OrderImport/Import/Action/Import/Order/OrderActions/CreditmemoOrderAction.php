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
use Magento\Sales\Model\Order\CreditmemoFactory;
use Magento\Sales\Model\Service\CreditmemoService;

class CreditmemoOrderAction implements OrderActionInterface
{
    /**
     * @var NotifyCustomerCreditmemo
     */
    private $notifyCustomerCreditmemo;

    /**
     * @var CreditmemoFactory
     */
    private $creditmemoFactory;

    /**
     * @var CreditmemoService
     */
    private $creditmemoService;

    public function __construct(
        NotifyCustomerCreditmemo $notifyCustomerCreditmemo,
        CreditmemoFactory $creditmemoFactory,
        CreditmemoService $creditmemoService
    ) {
        $this->notifyCustomerCreditmemo = $notifyCustomerCreditmemo;
        $this->creditmemoFactory = $creditmemoFactory;
        $this->creditmemoService = $creditmemoService;
    }

    public function execute(OrderInterface $order, array $actionData = []): void
    {
        if (!$order->canCreditmemo()
            || ($actionData['memo_order_new'] && !$actionData['isNew'])
        ) {
            return;
        }
        $creditmemo = $this->creditmemoFactory->createByOrder($order);
        $this->creditmemoService->refund($creditmemo);

        if ($actionData['notify_customer_memo'] ?? null) {
            $this->notifyCustomerCreditmemo->execute($order, $creditmemo);
        }
    }
}
