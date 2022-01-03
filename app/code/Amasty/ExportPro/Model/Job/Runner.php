<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types = 1);

namespace Amasty\ExportPro\Model\Job;

use Amasty\ExportCore\Api\Config\ProfileConfigInterface;
use Amasty\ExportCore\Processing\JobManager;
use Amasty\ExportPro\Api\CronJobRepositoryInterface;
use Amasty\ExportPro\Model\ModuleType;
use Amasty\ImportExportCore\Utils\Serializer;

class Runner
{
    /**
     * @var JobManager
     */
    private $jobManager;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var CronJobRepositoryInterface
     */
    private $jobRepository;

    public function __construct(
        CronJobRepositoryInterface $jobRepository,
        JobManager $jobManager,
        Serializer $serializer
    ) {
        $this->jobManager = $jobManager;
        $this->serializer = $serializer;
        $this->jobRepository = $jobRepository;
    }

    public function run(int $jobId, \Closure $profileConfigModifier = null): string
    {
        $processIdentity = $this->getProcessIdentity();
        $this->jobManager->requestJob($this->prepareProfileConfig($jobId, $profileConfigModifier), $processIdentity);

        return $processIdentity;
    }

    public function manualRun(int $jobId, \Closure $profileConfigModifier = null): string
    {
        $processIdentity = $this->getProcessIdentity();
        $profileConfig = $this->prepareProfileConfig($jobId, $profileConfigModifier);
        $profileConfig->getExtensionAttributes()->setManualRun(true);
        $this->jobManager->requestJob($profileConfig, $processIdentity);

        return $processIdentity;
    }

    public function prepareProfileConfig(int $jobId, \Closure $profileConfigModifier = null): ProfileConfigInterface
    {
        $job = $this->jobRepository->getById($jobId);
        $profileConfig = $job->getProfileConfig();
        $profileConfig->setModuleType(ModuleType::TYPE);
        $profileConfig->getExtensionAttributes()->setName($job->getTitle());
        $profileConfig->getExtensionAttributes()->setExternalId($job->getJobId());

        if ($profileConfigModifier) {
            $profileConfigModifier($profileConfig);
        }

        return $profileConfig;
    }

    protected function getProcessIdentity(): string
    {
        return uniqid('export_');
    }
}
