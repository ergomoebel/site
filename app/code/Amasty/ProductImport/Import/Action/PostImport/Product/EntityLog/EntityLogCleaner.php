<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImport
 */

declare(strict_types=1);

namespace Amasty\ProductImport\Import\Action\PostImport\Product\EntityLog;

use Amasty\ImportCore\Api\Action\CleanerInterface;
use Amasty\ImportCore\Api\ImportProcessInterface;
use Amasty\ProductImport\Model\EntityLog\EntityLogManager;

class EntityLogCleaner implements CleanerInterface
{
    /**
     * @var EntityLogManager
     */
    private $entityLogManager;

    public function __construct(EntityLogManager $entityLogManager)
    {
        $this->entityLogManager = $entityLogManager;
    }

    /**
     * @inheritDoc
     */
    public function clean(ImportProcessInterface $importProcess): void
    {
        $this->entityLogManager->cleanup($importProcess->getIdentity());
    }
}
