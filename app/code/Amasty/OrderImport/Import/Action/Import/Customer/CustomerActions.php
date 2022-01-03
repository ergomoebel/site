<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderImport
 */

declare(strict_types=1);

namespace Amasty\OrderImport\Import\Action\Import\Customer;

use Amasty\ImportCore\Api\ActionInterface;
use Amasty\ImportCore\Api\ImportProcessInterface;
use Amasty\ImportCore\Import\Source\SourceDataStructure;
use Amasty\OrderImport\Model\ModuleType;

class CustomerActions implements ActionInterface
{
    const BEHAVIOR_CODES = [
        'add',
        'add_direct',
        'addUpdate',
        'addUpdate_direct',
        'update',
        'update_direct'
    ];

    /**
     * @var CustomerStrategy|null
     */
    private $customerStrategy;

    /**
     * @var CustomerModeStrategyResolver
     */
    private $strategyResolver;

    public function __construct(
        CustomerModeStrategyResolver $strategyResolver
    ) {
        $this->strategyResolver = $strategyResolver;
    }

    public function initialize(ImportProcessInterface $importProcess): void
    {
        $configExtension = $importProcess->getProfileConfig()->getExtensionAttributes();

        $behavior = $importProcess->getProfileConfig()->getBehavior();
        if (!in_array($behavior, self::BEHAVIOR_CODES)
            || !$configExtension->getCustomerMode()
            || $importProcess->getProfileConfig()->getModuleType() !== ModuleType::TYPE
        ) {
            return;
        }
        $this->customerStrategy = $this->strategyResolver->resolveStrategy(
            (int)$configExtension->getCustomerMode()
        );
    }

    public function execute(ImportProcessInterface $importProcess): void
    {
        if (!$this->customerStrategy) {
            return;
        }
        $processedData = [];

        foreach ($importProcess->getData() as $index => $importOrderData) {
            if ($this->hasOrderData($importOrderData)) {
                $this->customerStrategy->execute($importOrderData);
            }
            $processedData[$index] = $importOrderData;
        }
        $importProcess->setData($processedData);
    }

    private function hasOrderData(array $data): bool
    {
        unset($data[SourceDataStructure::SUB_ENTITIES_DATA_KEY]);

        return count($data) > 0;
    }
}
