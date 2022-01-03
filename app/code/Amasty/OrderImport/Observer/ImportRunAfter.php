<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderImport
 */


declare(strict_types=1);

namespace Amasty\OrderImport\Observer;

use Amasty\ImportCore\Api\Config\ProfileConfigInterface;
use Amasty\ImportCore\Api\ImportProcessInterface;
use Amasty\ImportPro\Model\Notification\ImportAlertNotifier;
use Amasty\OrderImport\Model\ModuleType;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ImportRunAfter implements ObserverInterface
{
    /**
     * @var ImportAlertNotifier
     */
    private $alertNotifier;

    public function __construct(ImportAlertNotifier $alertNotifier)
    {
        $this->alertNotifier = $alertNotifier;
    }

    public function execute(Observer $observer)
    {
        /** @var ImportProcessInterface $importProcess */
        $importProcess = $observer->getData('importProcess');
        $profileConfig = $importProcess->getProfileConfig();

        if ($this->isNeedNotify($profileConfig)) {
            $this->alertNotifier->execute($importProcess);
        }
    }

    private function isNeedNotify(ProfileConfigInterface $profileConfig): bool
    {
        return $profileConfig->getModuleType() === ModuleType::TYPE
            && !$profileConfig->getExtensionAttributes()->getManualRun();
    }
}
