<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerImport
 */

declare(strict_types=1);

namespace Amasty\CustomerImport\Import\Action\Import\Customer;

use Amasty\CustomerImport\Api\CustomerActionInterface;
use Amasty\CustomerImport\Model\ModuleType;
use Amasty\CustomerImport\Model\Profile\Repository;
use Amasty\ImportCore\Api\ActionInterface;
use Amasty\ImportCore\Api\ImportProcessInterface;

class CustomerActions implements ActionInterface
{
    /**
     * @var CustomerActionProvider
     */
    private $actionProvider;

    /**
     * @var Repository
     */
    private $profileRepository;

    /**
     * @var CustomerActionInterface[]
     */
    private $actionsToPerform = [];

    public function __construct(
        CustomerActionProvider $actionProvider,
        Repository $profileRepository
    ) {
        $this->actionProvider = $actionProvider;
        $this->profileRepository = $profileRepository;
    }

    public function initialize(ImportProcessInterface $importProcess): void
    {
        $profileConfig = $importProcess->getProfileConfig();
        if ($profileConfig->getModuleType() !== ModuleType::TYPE) {
            return;
        }
        $profile = $this->profileRepository->getById(
            (int)$profileConfig->getExtensionAttributes()->getExternalId()
        );

        $this->actionsToPerform = $this->actionProvider->getActions($profile);
    }

    public function execute(ImportProcessInterface $importProcess): void
    {
        $customerResult = $importProcess->getProcessedEntityResult(
            $importProcess->getProfileConfig()->getEntityCode()
        );

        if (empty($this->actionsToPerform) || empty($customerResult->getAffectedIds())) {
            return;
        }

        foreach ($this->actionsToPerform as $action) {
            $action->execute($customerResult);
        }
    }
}
