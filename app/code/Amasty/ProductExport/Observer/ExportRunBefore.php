<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExport
 */


declare(strict_types=1);

namespace Amasty\ProductExport\Observer;

use Amasty\ExportCore\Api\ExportProcessInterface;
use Amasty\ProductExport\Api\ProfileRepositoryInterface;
use Amasty\ProductExport\Model\ModuleType;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ExportRunBefore implements ObserverInterface
{
    /**
     * @var ProfileRepositoryInterface
     */
    private $profileRepository;

    public function __construct(
        ProfileRepositoryInterface $profileRepository
    ) {
        $this->profileRepository = $profileRepository;
    }

    public function execute(Observer $observer)
    {
        try {
            /** @var ExportProcessInterface $exportProcess */
            $exportProcess = $observer->getData('exportProcess');
            if ($exportProcess->getProfileConfig()->getModuleType() !== ModuleType::TYPE) {
                return;
            }
            $profileConfig = $exportProcess->getProfileConfig();
            $profileId = (int)$profileConfig->getExtensionAttributes()->getExternalId();
            $this->profileRepository->updateLastExported($profileId);
        } catch (\Exception $e) {
            null;
        }
    }
}
