<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderImport
 */

declare(strict_types=1);

namespace Amasty\OrderImport\Import\Action\Import\Order\OrderActions;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Model\Order\Email\Sender\CreditmemoSender;

class NotifyCustomerCreditmemo
{
    /**
     * @var CreditmemoSender
     */
    private $creditmemoSender;

    /**
     * @var State
     */
    private $appState;

    public function __construct(
        CreditmemoSender $creditmemoSender,
        State $appState
    ) {
        $this->appState = $appState;
        $this->creditmemoSender = $creditmemoSender;
    }

    public function execute(OrderInterface $order, CreditmemoInterface $creditmemo)
    {
        if ((int)$creditmemo->getOrderId() !== (int)$order->getId()) {
            return;
        }
        //need to emulate frontend area because of theme full path problem
        // in \Magento\Framework\View\Design\Fallback\Rule\Theme::getPatternDirs
        $this->appState->emulateAreaCode(Area::AREA_FRONTEND, [$this->creditmemoSender, 'send'], [$creditmemo]);
        $order->addCommentToStatusHistory(
            __('The customer was notified about the credit memo creation.')
        )->setIsCustomerNotified(true);
    }
}
