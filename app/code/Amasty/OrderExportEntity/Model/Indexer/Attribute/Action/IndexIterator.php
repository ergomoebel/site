<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExportEntity
 */

declare(strict_types=1);

namespace Amasty\OrderExportEntity\Model\Indexer\Attribute\Action;

class IndexIterator implements \Iterator
{
    /**
     * @var array
     */
    private $current;

    /**
     * @var int
     */
    private $key;

    /**
     * @var bool
     */
    private $valid = true;

    /**
     * @var array
     */
    private $orderItems = [];

    /**
     * @var array
     */
    private $attributes;

    /**
     * @var array
     */
    private $staticFields;

    /**
     * @var DataProvider
     */
    private $dataProvider;

    /**
     * @var int
     */
    private $page = 1;

    /**
     * @var array
     */
    private $itemsIds = [];

    public function __construct(
        DataProvider $dataProvider,
        array $itemsIds,
        array $staticFields,
        array $attributes
    ) {
        $this->dataProvider = $dataProvider;
        $this->itemsIds = $itemsIds;
        $this->attributes = $attributes;
        $this->staticFields = $staticFields;
    }

    public function current(): array
    {
        return $this->current;
    }

    public function next()
    {
        \next($this->orderItems);

        if (\key($this->orderItems) === null) {
            $this->updateOrderItems();
        }

        $this->key = null;

        if ($this->valid()) {
            $this->current = \current($this->orderItems);
            $this->key = $this->current['order_item_id'];
        }
    }

    private function updateOrderItems()
    {
        $this->orderItems = $this->dataProvider->getOrderItemsForIndex(
            $this->staticFields,
            $this->attributes,
            $this->itemsIds,
            $this->page
        );

        if (!count($this->orderItems)) {
            $this->valid = false;
            return;
        }

        $this->page++;
        \reset($this->orderItems);
    }

    public function key(): int
    {
        return $this->key;
    }

    public function valid(): bool
    {
        return $this->valid;
    }

    public function rewind()
    {
        $this->page = 1;
        $this->key = null;
        $this->current = null;
        unset($this->orderItems);
        $this->orderItems = [];
        $this->next();
    }
}
