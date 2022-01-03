<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */


declare(strict_types=1);

namespace Amasty\ImportPro\Observer;

use Amasty\ImportCore\Api\Config\ProfileConfigInterface;
use Amasty\ImportCore\Api\ImportProcessInterface;
use Amasty\ImportPro\Model\History\HistoryStrategies;
use Amasty\ImportPro\Model\History\Repository as HistoryRepository;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class ImportRunBefore implements ObserverInterface
{
    /**
     * @var HistoryRepository
     */
    private $historyRepository;

    /**
     * @var HistoryStrategies
     */
    private $historyStrategies;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        HistoryRepository $historyRepository,
        HistoryStrategies $historyStrategies,
        LoggerInterface $logger
    ) {
        $this->historyRepository = $historyRepository;
        $this->historyStrategies = $historyStrategies;
        $this->logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            /** @var ImportProcessInterface $importProcess */
            $importProcess = $observer->getData('importProcess');
            $profileConfig = $importProcess->getProfileConfig();
            if ($this->historyStrategies->isLogStrategy($profileConfig->getStrategy())) {
                $historyModel = $this->historyRepository->getEmptyHistoryModel();
                $now = new \DateTime('now', new \DateTimeZone('utc'));
                $historyModel->setStartedAt($now->format('Y-m-d H:i:s'));
                $historyModel->setType($profileConfig->getModuleType());
                $historyModel->setEntityCode($profileConfig->getEntityCode());
                $historyModel->setJobId($profileConfig->getExtensionAttributes()->getExternalId());
                $historyModel->setName($profileConfig->getExtensionAttributes()->getName());
                $historyModel->setIdentity($importProcess->getIdentity());

                $this->historyRepository->save($historyModel);
            }
        } catch (\Exception $e) {
            $this->logger->error($e);
        }
    }
}
