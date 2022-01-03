<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExportEntity
 */

declare(strict_types=1);

namespace Amasty\OrderExportEntity\Model\Indexer\CustomOption;

use Amasty\OrderExportEntity\Model\Indexer\CustomOption\Action\DataProvider;
use Psr\Log\LoggerInterface;

/**
 * Custom options indexer action
 */
class Action
{
    /**
     * @var DataProvider
     */
    private $dataProvider;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param DataProvider $dataProvider
     * @param LoggerInterface $logger
     */
    public function __construct(
        DataProvider $dataProvider,
        LoggerInterface $logger
    ) {
        $this->dataProvider = $dataProvider;
        $this->logger = $logger;
    }

    /**
     * Rebuild index
     *
     * @param array $ids
     * @return \Generator
     */
    public function rebuildIndex(array $ids = [])
    {
        $lastOrderItemId = 0;

        try {
            $orderItems = $this->dataProvider->getOrderItemsForIndex($ids, $lastOrderItemId);
            while (count($orderItems) > 0) {
                foreach ($orderItems as $orderItem) {
                    $lastOrderItemId = $orderItem['item_id'];
                    $index = $this->dataProvider->prepareCustomOptionsIndex($orderItem);

                    yield $orderItem['item_id'] => $index;
                }

                $orderItems = $this->dataProvider->getOrderItemsForIndex($ids, $lastOrderItemId);
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
    }
}
