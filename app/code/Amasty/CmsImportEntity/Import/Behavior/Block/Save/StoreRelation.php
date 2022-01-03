<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CmsImportEntity
 */


declare(strict_types=1);

namespace Amasty\CmsImportEntity\Import\Behavior\Block\Save;

use Amasty\CmsImportEntity\Model\ResourceModel\Store\RelationPersistence;
use Amasty\ImportCore\Api\BehaviorInterface;
use Amasty\ImportCore\Api\Behavior\BehaviorResultInterface;
use Amasty\ImportCore\Api\Behavior\BehaviorResultInterfaceFactory;
use Magento\Cms\Api\Data\BlockInterface;

class StoreRelation implements BehaviorInterface
{
    /**
     * @var BehaviorResultInterfaceFactory
     */
    private $resultFactory;

    /**
     * @var RelationPersistence
     */
    private $relationPersistence;

    public function __construct(
        BehaviorResultInterfaceFactory $resultFactory,
        RelationPersistence $relationPersistence
    ) {
        $this->resultFactory = $resultFactory;
        $this->relationPersistence = $relationPersistence;
    }

    /**
     * @inheritDoc
     */
    public function execute(array &$data, ?string $customIdentifier = null): BehaviorResultInterface
    {
        $this->relationPersistence->save($data, BlockInterface::class);

        return $this->resultFactory->create();
    }
}
