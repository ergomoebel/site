<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Observer;

use Amasty\ExportCore\Api\Config\ProfileConfigInterface;
use Amasty\ExportCore\Api\ExportProcessInterface;
use Amasty\ExportCore\Api\ExportResultInterface;
use Amasty\ExportPro\Model\LastExportedId\Repository as LastExportedIdRepository;
use Amasty\ExportPro\Model\History\HistoryStrategies;
use Amasty\ExportPro\Model\History\Repository as HistoryRepository;
use Amasty\ExportPro\Model\OptionSource\HistoryStatus;
use Magento\Framework\Event\ObserverInterface;

class ExportRunAfter implements ObserverInterface
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
     * @var LastExportedIdRepository
     */
    private $lastExportedIdRepository;

    public function __construct(
        HistoryRepository $historyRepository,
        HistoryStrategies $historyStrategies,
        LastExportedIdRepository $lastExportedIdRepository
    ) {
        $this->historyRepository = $historyRepository;
        $this->historyStrategies = $historyStrategies;
        $this->lastExportedIdRepository = $lastExportedIdRepository;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            /** @var ExportProcessInterface $exportProcess */
            $exportProcess = $observer->getData('exportProcess');
            $profileConfig = $exportProcess->getProfileConfig();
            $exportResult = $exportProcess->getExportResult();
            $strategy = $profileConfig->getStrategy();
            $historyModel = $this->historyRepository->getByIdentity($exportProcess->getIdentity());

            if (!empty($strategy) && $this->historyStrategies->isLogStrategy($strategy)
                && $historyModel->getHistoryId()
            ) {
                $now = new \DateTime('now', new \DateTimeZone('utc'));
                $historyModel->setFinishedAt($now->format('Y-m-d H:i:s'));
                $historyModel->setStatus($exportResult->isFailed() ? HistoryStatus::FAILED : HistoryStatus::SUCCESS);
                $historyModel->setLog($exportResult->serialize());

                $this->historyRepository->save($historyModel);
            }

            if ($profileConfig->getExtensionAttributes()->getExportNewEntities() && !$exportResult->isFailed()) {
                $externalId = $profileConfig->getExtensionAttributes()->getExternalId();
                $exportModule = $profileConfig->getModuleType();
                $lastExportedId = $this->lastExportedIdRepository->getByTypeAndExternalId($exportModule, $externalId);
                $lastId = $profileConfig->getExtensionAttributes()->getLastExportedId();
                if ($lastId && $lastId != $lastExportedId->getLastExportedId()) {
                    $lastExportedId->setLastExportedId($lastId);
                    $lastExportedId->setExternalId($externalId);
                    $lastExportedId->setType($exportModule);
                    $this->lastExportedIdRepository->save($lastExportedId);
                }
            }
        } catch (\Exception $e) {
            null;
        }
    }
}
