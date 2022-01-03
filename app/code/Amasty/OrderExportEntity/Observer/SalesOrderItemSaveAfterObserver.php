<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExportEntity
 */

declare(strict_types=1);

namespace Amasty\OrderExportEntity\Observer;

use Amasty\OrderExportEntity\Model\Indexer\Attribute as AttributeIndexer;
use Amasty\OrderExportEntity\Model\Indexer\CustomOption as CustomOptionIndexer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Indexer\IndexerRegistry;

class SalesOrderItemSaveAfterObserver implements ObserverInterface
{
    /**
     * @var IndexerRegistry
     */
    private $indexerRegistry;

    /**
     * @var array
     */
    private $indexers= [
        AttributeIndexer::INDEXER_ID,
        CustomOptionIndexer::INDEXER_ID
    ];

    public function __construct(IndexerRegistry $indexerRegistry)
    {
        $this->indexerRegistry = $indexerRegistry;
    }

    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Item $orderItem */
        $orderItem = $observer->getEvent()->getItem();
        $itemId = $orderItem->getId();
        if ($itemId) {
            foreach ($this->indexers as $indexerId) {
                $indexer = $this->indexerRegistry->get($indexerId);
                if (!$indexer->isScheduled()) {
                    $indexer->reindexRow($itemId);
                }
            }
        }
    }
}
