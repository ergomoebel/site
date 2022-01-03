<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Model\Notification;

use Amasty\ExportCore\Api\ExportProcessInterface;
use Amasty\ExportPro\Api\Export\NotifierInterface;
use Psr\Log\LoggerInterface;

class ExportAlertNotifier
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
                    sprintf('Export Notifier "%s" must implement %s', $name, NotifierInterface::class)
                );
            }
        }

        $this->notifiers = $notifiers;
        $this->logger = $logger;
    }

    public function execute(ExportProcessInterface $exportProcess)
    {
        foreach ($this->notifiers as $notifier) {
            try {
                $notifier->notify($exportProcess);
            } catch (\Throwable $e) {
                $this->logger->error(__('Export: Unable to send notification. Error is: ' . $e->getMessage()));
            }
        }
    }
}
