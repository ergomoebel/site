<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImport
 */

declare(strict_types=1);

namespace Amasty\ProductImport\Import\Action\PostImport\Product\EntityLog;

use Amasty\ImportCore\Api\ActionInterface;
use Amasty\ImportCore\Api\Behavior\BehaviorResultInterface;
use Amasty\ImportCore\Api\ImportProcessInterface;
use Amasty\ProductImport\Api\Data\ProfileInterface;
use Amasty\ProductImport\Model\EntityLog\EntityLogManager;
use Amasty\ProductImport\Model\ModuleType;
use Amasty\ProductImport\Model\Profile\Repository;

class CollectAction implements ActionInterface
{
    /**
     * @var EntityLogManager
     */
    private $entityLogManager;

    /**
     * @var Repository
     */
    private $profileRepository;

    public function __construct(
        EntityLogManager $entityLogManager,
        Repository $profileRepository
    ) {
        $this->entityLogManager = $entityLogManager;
        $this->profileRepository = $profileRepository;
    }

    public function execute(ImportProcessInterface $importProcess): void
    {
        $profileConfig = $importProcess->getProfileConfig();
        if ($profileConfig->getModuleType() !== ModuleType::TYPE) {
            return;
        }

        /** @var BehaviorResultInterface $behaviorResult */
        $behaviorResult = $importProcess->getProcessedEntityResult($profileConfig->getEntityCode());
        $affectedIds = $behaviorResult->getAffectedIds();
        if (!$affectedIds) {
            return;
        }

        $profile = $this->profileRepository->getById(
            (int)$profileConfig->getExtensionAttributes()->getExternalId()
        );
        if (!$this->isProductActionEnabled('disable_products', $profile)) {
            return;
        }

        $this->entityLogManager->addEntries($affectedIds, $importProcess->getIdentity());
    }

    //phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedFunction
    public function initialize(ImportProcessInterface $importProcess): void
    {
    }

    /**
     * Checks if specified product action is enabled in the product import profile
     *
     * @param string $actionName
     * @param ProfileInterface $profile
     * @return bool
     */
    private function isProductActionEnabled(string $actionName, ProfileInterface $profile): bool
    {
        $productActions = $profile->getProductActions();
        if (!isset($productActions[$actionName])) {
            return false;
        }

        $value = (int)$productActions[$actionName]['options']['value'] ?? 0;
        if (!$value) {
            return false;
        }

        return true;
    }
}
