<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImport
 */


declare(strict_types = 1);

namespace Amasty\ProductImport\Model\Profile;

use Amasty\ImportCore\Api\Config\ProfileConfigInterface;
use Amasty\ImportCore\Processing\JobManager;
use Amasty\ImportExportCore\Utils\Serializer;
use Amasty\ProductImport\Api\ProfileRepositoryInterface;
use Amasty\ProductImport\Model\ConfigProvider;
use Amasty\ProductImport\Model\ModuleType;

class ProfileRunner
{
    /**
     * @var ProfileRepositoryInterface
     */
    private $profileRepository;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var JobManager
     */
    private $jobManager;

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(
        ProfileRepositoryInterface $profileRepository,
        ConfigProvider $configProvider,
        JobManager $jobManager,
        Serializer $serializer
    ) {
        $this->profileRepository = $profileRepository;
        $this->configProvider = $configProvider;
        $this->jobManager = $jobManager;
        $this->serializer = $serializer;
    }

    public function run(int $profileId, \Closure $profileConfigModifier = null): string
    {
        $processIdentity = $this->getProcessIdentity();
        $this->jobManager->requestJob(
            $this->prepareProfileConfig($profileId, $profileConfigModifier),
            $processIdentity
        );

        return $processIdentity;
    }

    public function manualRun(int $profileId, \Closure $profileConfigModifier = null): string
    {
        $processIdentity = $this->getProcessIdentity();
        $profileConfig = $this->prepareProfileConfig($profileId, $profileConfigModifier);
        $profileConfig->getExtensionAttributes()->setManualRun(true);
        $this->jobManager->requestJob($profileConfig, $processIdentity);

        return $processIdentity;
    }

    public function prepareProfileConfig(int $profileId, \Closure $profileConfigModifier = null): ProfileConfigInterface
    {
        $profile = $this->profileRepository->getById($profileId);
        $profileConfig = $profile->getConfig();
        $profileConfig->setModuleType(ModuleType::TYPE);
        $profileConfig->getExtensionAttributes()->setName($profile->getName());
        $profileConfig->getExtensionAttributes()->setExternalId($profile->getId());
        $profileConfig->setIsUseMultiProcess($this->configProvider->useMultiProcess());
        $profileConfig->setMaxJobs($this->configProvider->getMaxProcessCount());

        if ($profileConfig->getBatchSize() === null) {
            $profileConfig->setBatchSize($this->configProvider->getBatchSize());
        }

        if ($profileConfigModifier) {
            $profileConfigModifier($profileConfig);
        }

        return $profileConfig;
    }

    protected function getProcessIdentity(): string
    {
        return uniqid('product_import_');
    }
}
