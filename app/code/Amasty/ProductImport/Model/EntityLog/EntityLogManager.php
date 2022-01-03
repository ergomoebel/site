<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImport
 */


declare(strict_types=1);

namespace Amasty\ProductImport\Model\EntityLog;

use Amasty\ProductImport\Model\EntityLog\ResourceModel\EntityLog as EntityLogResource;
use Magento\Framework\Exception\CouldNotSaveException;

class EntityLogManager
{
    /**
     * @var EntityLogFactory
     */
    private $entityLogFactory;

    /**
     * @var EntityLogResource
     */
    private $entityLogResource;

    public function __construct(
        EntityLogFactory $entityLogFactory,
        EntityLogResource $entityLogResource
    ) {
        $this->entityLogFactory = $entityLogFactory;
        $this->entityLogResource = $entityLogResource;
    }

    /**
     * Add new entity log entries
     *
     * @param array $entityIds
     * @param string $processIdentity
     * @throws CouldNotSaveException
     */
    public function addEntries(array $entityIds, string $processIdentity): void
    {
        // todo: optimize, minimize sql requests
        foreach ($entityIds as $entityId) {
            /** @var EntityLog $logEntry */
            $logEntry = $this->entityLogFactory->create();
            try {
                $this->entityLogResource->loadByEntityIdAndIdentity(
                    $logEntry,
                    (int)$entityId,
                    $processIdentity
                );
                if (!$logEntry->getId()) {
                    $logEntry->setEntityId($entityId)
                        ->setProcessIdentity($processIdentity);

                    $this->entityLogResource->save($logEntry);
                }
            } catch (\Exception $e) {
                if ($logEntry->getId()) {
                    throw new CouldNotSaveException(
                        __(
                            'Unable to save entity log entry with ID %1. Error: %2',
                            [$logEntry->getId(), $e->getMessage()]
                        )
                    );
                }
                throw new CouldNotSaveException(
                    __('Unable to save new entity log entry. Error: %1', $e->getMessage())
                );
            }
        }
    }

    /**
     * Cleanup log entries of given process identity
     *
     * @param string $processIdentity
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    public function cleanup(string $processIdentity): void
    {
        $this->entityLogResource->deleteEntriesByProcessIdentity($processIdentity);
    }
}
