<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */


namespace Amasty\ImportPro\Cron;

use Amasty\ImportPro\Model\Job\Runner;
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
     * @var Runner
     */
    private $runner;

    public function __construct(
        Runner $runner,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->runner = $runner;
    }

    public function execute(Observer $observer)
    {
        if ($jobId = $observer->getData('external_id')) {
            try {
                $this->runner->run($jobId);
            } catch (LocalizedException $e) {
                $this->logger->critical($e);
            }
        }
    }
}
