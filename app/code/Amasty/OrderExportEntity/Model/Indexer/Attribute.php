<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExportEntity
 */

declare(strict_types=1);

namespace Amasty\OrderExportEntity\Model\Indexer;

use Amasty\OrderExportEntity\Model\Indexer\Attribute\Action\FullFactory;
use Amasty\OrderExportEntity\Model\Indexer\Attribute\IndexerHandler;
use Amasty\OrderExportEntity\Model\Indexer\Attribute\IndexerHandlerFactory;
use Magento\Framework\Indexer\ActionInterface as IndexerActionInterface;
use Magento\Framework\Mview\ActionInterface as MviewActionInterface;

class Attribute implements IndexerActionInterface, MviewActionInterface
{
    const INDEXER_ID = 'amasty_order_export_attribute_index';

    /**
     * @var IndexerHandlerFactory
     */
    private $indexerHandlerFactory;

    /**
     * @var Attribute\Action\Full
     */
    private $fullAction;

    /**
     * @var array
     */
    protected $data;

    public function __construct(
        FullFactory $fullActionFactory,
        IndexerHandlerFactory $indexerHandlerFactory,
        array $data = ['indexer_id' => self::INDEXER_ID]
    ) {
        $this->fullAction = $fullActionFactory->create(['data' => $data]);
        $this->indexerHandlerFactory = $indexerHandlerFactory;
        $this->data = $data;
    }

    public function executeFull()
    {
        $this->execute([]);
    }

    public function executeList(array $ids)
    {
        $this->execute($ids);
    }

    public function executeRow($id)
    {
        $this->execute([$id]);
    }

    public function execute($ids)
    {
        /** @var IndexerHandler $indexHandler */
        $indexHandler = $this->indexerHandlerFactory->create([
            'data' => $this->data
        ]);

        if (!count($ids)) {
            $indexHandler->cleanIndex([]);
        } else {
            $indexHandler->updateIndex([]);
        }

        $attributes = $indexHandler->getIndexedAttributesHash([]);
        $indexHandler->setAttributeHash($attributes);
        $indexHandler->saveIndex(
            [],
            $this->fullAction->rebuildIndex(
                $attributes,
                $ids
            )
        );
    }
}
