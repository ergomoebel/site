<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


declare(strict_types=1);

namespace Amasty\OrderExport\Observer;

use Amasty\ExportCore\Api\Config\ProfileConfigInterface;
use Amasty\ExportCore\Api\ExportProcessInterface;
use Amasty\ExportPro\Model\Notification\ExportAlertNotifier;
use Amasty\OrderExport\Model\ModuleType;
use Magento\Framework\Event\ObserverInterface;

class ExportRunAfter implements ObserverInterface
{
    /**
     * @var ExportAlertNotifier
     */
    private $alertNotifier;

    public function __construct(ExportAlertNotifier $alertNotifier)
    {
        $this->alertNotifier = $alertNotifier;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var ExportProcessInterface $exportProcess */
        $exportProcess = $observer->getData('exportProcess');
        $profileConfig = $exportProcess->getProfileConfig();
        $exportResult = $exportProcess->getExportResult();

        if ($exportResult->isFailed() && $this->isNeedNotify($profileConfig)) {
            $this->alertNotifier->execute($exportProcess);
        }
    }

    private function isNeedNotify(ProfileConfigInterface $profileConfig): bool
    {
        return $profileConfig->getModuleType() === ModuleType::TYPE
            && !$profileConfig->getExtensionAttributes()->getManualRun();
    }
}
