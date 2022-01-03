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
use Amasty\ExportPro\Model\History\HistoryStrategies;
use Amasty\ExportPro\Model\History\Repository as HistoryRepository;
use Amasty\ExportPro\Model\OptionSource\HistoryStatus;
use Magento\Framework\Event\ObserverInterface;

class ExportRunBefore implements ObserverInterface
{
    /**
     * @var HistoryRepository
     */
    private $historyRepository;

    /**
     * @var HistoryStrategies
     */
    private $historyStrategies;

    public function __construct(
        HistoryRepository $historyRepository,
        HistoryStrategies $historyStrategies
    ) {
        $this->historyRepository = $historyRepository;
        $this->historyStrategies = $historyStrategies;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            /** @var ExportProcessInterface $exportProcess */
            $exportProcess = $observer->getData('exportProcess');
            $profileConfig = $exportProcess->getProfileConfig();
            if ($this->historyStrategies->isLogStrategy($profileConfig->getStrategy())) {
                $historyModel = $this->historyRepository->getEmptyHistoryModel();
                $now = new \DateTime('now', new \DateTimeZone('utc'));
                $historyModel->setExportedAt($now->format('Y-m-d H:i:s'));
                $historyModel->setStatus(HistoryStatus::PROCESSING);
                $historyModel->setEntityCode($profileConfig->getEntityCode());
                $historyModel->setType($profileConfig->getModuleType());
                $historyModel->setJobId($profileConfig->getExtensionAttributes()->getExternalId());
                $historyModel->setName($profileConfig->getExtensionAttributes()->getName());
                $historyModel->setIdentity($exportProcess->getIdentity());

                $this->historyRepository->save($historyModel);
            }
        } catch (\Exception $e) {
            null;
        }
    }
}
