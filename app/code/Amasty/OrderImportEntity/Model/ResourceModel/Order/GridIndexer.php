<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderImportEntity
 */

declare(strict_types=1);

namespace Amasty\OrderImportEntity\Model\ResourceModel\Order;

use Magento\Sales\Model\ResourceModel\AbstractGrid;
use Magento\Sales\Model\ResourceModel\GridInterface;

class GridIndexer
{
    /**
     * @var GridInterface
     */
    private $orderGrid;

    /**
     * @var AbstractGrid[]
     */
    private $relatedEntitiesGrids;

    public function __construct(
        GridInterface $orderGrid,
        array $relatedEntitiesGrids = []
    ) {
        $this->orderGrid = $orderGrid;
        $this->relatedEntitiesGrids = $relatedEntitiesGrids;
    }

    public function update($value): void
    {
        $this->orderGrid->refresh($value);
    }

    public function delete($value): void
    {
        $this->orderGrid->purge($value);
        foreach ($this->relatedEntitiesGrids as $grid) {
            $this->deleteRelatedGridRows($grid, $value);
        }
    }

    /**
     * Delete rows from related grid
     *
     * @param AbstractGrid $grid
     * @param int|string $value
     * @return void
     */
    private function deleteRelatedGridRows(AbstractGrid $grid, $value): void
    {
        $grid->getConnection()->delete(
            $grid->getGridTable(),
            ['order_id = ?' => $value]
        );
    }
}
