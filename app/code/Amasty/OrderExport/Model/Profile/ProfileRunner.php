<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


declare(strict_types = 1);

namespace Amasty\OrderExport\Model\Profile;

use Amasty\ExportCore\Api\Config\ProfileConfigInterface;
use Amasty\ExportCore\Processing\JobManager;
use Amasty\ImportExportCore\Utils\Serializer;
use Amasty\OrderExport\Api\ProfileRepositoryInterface;
use Amasty\OrderExport\Model\ConfigProvider;
use Amasty\OrderExport\Model\ModuleType;

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

        if ($profileConfigModifier) {
            $profileConfigModifier($profileConfig);
        }

        return $profileConfig;
    }

    protected function getProcessIdentity(): string
    {
        return uniqid('order_export_');
    }
}
