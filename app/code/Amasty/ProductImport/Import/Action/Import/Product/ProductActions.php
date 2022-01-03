<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImport
 */

declare(strict_types=1);

namespace Amasty\ProductImport\Import\Action\Import\Product;

use Amasty\ProductImport\Api\ProductActionInterface;
use Amasty\ProductImport\Model\ModuleType;
use Amasty\ProductImport\Model\Profile\Repository;
use Amasty\ImportCore\Api\ActionInterface;
use Amasty\ImportCore\Api\ImportProcessInterface;

class ProductActions implements ActionInterface
{
    /**
     * @var ProductActionProvider
     */
    private $actionProvider;

    /**
     * @var Repository
     */
    private $profileRepository;

    /**
     * @var ProductActionInterface[]
     */
    private $actionsToPerform = [];

    /**
     * @var string
     */
    protected $actionGroup = ProductActionInterface::GROUP_BATCH;

    public function __construct(
        ProductActionProvider $actionProvider,
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

        $this->actionsToPerform = $this->actionProvider->getActions($profile, $this->actionGroup);
    }

    public function execute(ImportProcessInterface $importProcess): void
    {
        if (empty($this->actionsToPerform)) {
            return;
        }

        foreach ($this->actionsToPerform as $action) {
            $action->execute($importProcess);
        }
    }
}
