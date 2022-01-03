<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */


declare(strict_types = 1);

namespace Amasty\ImportPro\Model\Job;

use Amasty\ImportCore\Api\Config\ProfileConfigInterface;
use Amasty\ImportCore\Processing\JobManager;
use Amasty\ImportPro\Api\CronJobRepositoryInterface;
use Amasty\ImportPro\Model\ModuleType;

class Runner
{
    /**
     * @var JobManager
     */
    private $jobManager;

    /**
     * @var CronJobRepositoryInterface
     */
    private $jobRepository;

    public function __construct(
        CronJobRepositoryInterface $jobRepository,
        JobManager $jobManager
    ) {
        $this->jobManager = $jobManager;
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
        return uniqid('import_');
    }
}
