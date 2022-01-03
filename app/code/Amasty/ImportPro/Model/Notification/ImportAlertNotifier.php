<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */


declare(strict_types=1);

namespace Amasty\ImportPro\Model\Notification;

use Amasty\ImportCore\Api\ImportProcessInterface;
use Amasty\ImportPro\Api\Import\NotifierInterface;
use Psr\Log\LoggerInterface;

class ImportAlertNotifier
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var NotifierInterface[]
     */
    private $notifiers;

    public function __construct(
        LoggerInterface $logger,
        array $notifiers = []
    ) {
        foreach ($notifiers as $name => $notifier) {
            if (!$notifier instanceof NotifierInterface) {
                throw new \LogicException(
                    sprintf('Import Notifier "%s" must implement %s', $name, NotifierInterface::class)
                );
            }
        }

        $this->notifiers = $notifiers;
        $this->logger = $logger;
    }

    public function execute(ImportProcessInterface $importProcess)
    {
        foreach ($this->notifiers as $notifier) {
            try {
                $notifier->notify($importProcess);
            } catch (\Throwable $e) {
                $this->logger->error(__('Import: Unable to send notification. Error is: ' . $e->getMessage()));
            }
        }
    }
}
