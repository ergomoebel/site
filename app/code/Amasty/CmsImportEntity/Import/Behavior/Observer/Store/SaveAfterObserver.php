<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CmsImportEntity
 */

declare(strict_types=1);

namespace Amasty\CmsImportEntity\Import\Behavior\Observer\Store;

use Amasty\CmsImportEntity\Model\ResourceModel\Store\RelationPersistence;
use Amasty\ImportCore\Api\Behavior\BehaviorObserverInterface;

class SaveAfterObserver implements BehaviorObserverInterface
{
    /**
     * @var RelationPersistence
     */
    private $relationPersistence;

    /**
     * @var string
     */
    private $entityType;

    public function __construct(
        RelationPersistence $relationPersistence,
        array $config
    ) {
        if (!isset($config['entityType'])) {
            throw new \LogicException('entityType is not specified.');
        }
        $this->entityType = $config['entityType'];
        $this->relationPersistence = $relationPersistence;
    }

    /**
     * @inheritDoc
     */
    public function execute(array &$data): void
    {
        $this->relationPersistence->saveDefault($data, $this->entityType);
    }
}
