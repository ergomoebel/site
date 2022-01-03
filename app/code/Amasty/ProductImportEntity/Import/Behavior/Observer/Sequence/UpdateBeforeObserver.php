<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */

declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\Behavior\Observer\Sequence;

use Amasty\ImportCore\Api\Behavior\BehaviorObserverInterface;
use Amasty\ProductImportEntity\Model\EntityManager\SequenceHandler;

class UpdateBeforeObserver implements BehaviorObserverInterface
{
    /**
     * @var string
     */
    private $entityType;

    /**
     * @var SequenceHandler
     */
    private $sequenceHandler;

    public function __construct(SequenceHandler $sequenceHandler, array $config)
    {
        if (!isset($config['entityType'])) {
            throw new \LogicException('entityType is not specified.');
        }
        $this->entityType = $config['entityType'];
        $this->sequenceHandler = $sequenceHandler;
    }

    /**
     * @inheritDoc
     */
    public function execute(array &$data): void
    {
        $this->sequenceHandler->handleUpdate($data, $this->entityType);
    }
}
