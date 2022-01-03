<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExportEntity
 */

declare(strict_types=1);

namespace Amasty\OrderExportEntity\Model\Indexer;

use Amasty\OrderExportEntity\Model\Indexer\CustomOption\Action;
use Amasty\OrderExportEntity\Model\Indexer\CustomOption\IndexerHandlerFactory;
use Magento\Framework\Indexer\ActionInterface as IndexerActionInterface;
use Magento\Framework\Indexer\SaveHandler\IndexerInterface;
use Magento\Framework\Mview\ActionInterface as MviewActionInterface;

/**
 * Custom options indexer
 */
class CustomOption implements IndexerActionInterface, MviewActionInterface
{
    const INDEXER_ID = 'amasty_order_export_custom_option_index';

    /**
     * @var IndexerHandlerFactory
     */
    private $indexerHandlerFactory;

    /**
     * @var Action
     */
    private $indexAction;

    /**
     * @var array
     */
    private $data;

    public function __construct(
        IndexerHandlerFactory $indexerHandlerFactory,
        Action $indexAction,
        array $data = ['indexer_id' => self::INDEXER_ID]
    ) {
        $this->indexerHandlerFactory = $indexerHandlerFactory;
        $this->indexAction = $indexAction;
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
        /** @var IndexerInterface $indexHandler */
        $indexHandler = $this->indexerHandlerFactory->create([
            'data' => $this->data
        ]);
        if (!count($ids)) {
            $indexHandler->cleanIndex([]);
        } else {
            $indexHandler->deleteIndex([], new \ArrayIterator($ids));
        }

        $indexHandler->saveIndex([], $this->indexAction->rebuildIndex($ids));
    }
}
