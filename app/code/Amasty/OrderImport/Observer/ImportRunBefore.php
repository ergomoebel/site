<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderImport
 */

declare(strict_types=1);

namespace Amasty\OrderImport\Observer;

use Amasty\ImportCore\Api\ImportProcessInterface;
use Amasty\OrderImport\Model\ModuleType;
use Amasty\OrderImport\Model\Profile\Repository;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ImportRunBefore implements ObserverInterface
{
    /**
     * @var Repository
     */
    private $profileRepository;

    public function __construct(
        Repository $profileRepository
    ) {
        $this->profileRepository = $profileRepository;
    }

    public function execute(Observer $observer)
    {
        try {
            /** @var ImportProcessInterface $importProcess */
            $importProcess = $observer->getData('importProcess');
            if ($importProcess->getProfileConfig()->getModuleType() !== ModuleType::TYPE) {
                return;
            }

            $profileConfig = $importProcess->getProfileConfig();
            $profileId = (int)$profileConfig->getExtensionAttributes()->getExternalId();
            $this->profileRepository->updateLastImported($profileId);
        } catch (\Exception $e) {
            null;
        }
    }
}
