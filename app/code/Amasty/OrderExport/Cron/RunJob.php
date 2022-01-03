<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


namespace Amasty\OrderExport\Cron;

use Amasty\OrderExport\Model\Profile\ProfileRunner;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class RunJob implements ObserverInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ProfileRunner
     */
    private $runProfile;

    public function __construct(
        LoggerInterface $logger,
        ProfileRunner $runProfile
    ) {
        $this->logger = $logger;
        $this->runProfile = $runProfile;
    }

    public function execute(Observer $observer)
    {
        if ($profileId = $observer->getData('external_id')) {
            try {
                $this->runProfile->run((int)$profileId);
            } catch (LocalizedException $e) {
                $this->logger->critical($e);
            }
        }
    }
}
