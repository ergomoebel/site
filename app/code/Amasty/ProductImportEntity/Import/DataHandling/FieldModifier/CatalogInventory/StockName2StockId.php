<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\DataHandling\FieldModifier\CatalogInventory;

use Amasty\ImportCore\Api\Modifier\FieldModifierInterface;
use Magento\CatalogInventory\Api\StockCriteriaInterface;
use Magento\CatalogInventory\Api\StockCriteriaInterfaceFactory;
use Magento\CatalogInventory\Api\StockRepositoryInterface;

class StockName2StockId implements FieldModifierInterface
{
    /**
     * @var StockCriteriaInterfaceFactory
     */
    private $stockCriteriaFactory;

    /**
     * @var StockRepositoryInterface
     */
    private $stockRepository;

    /**
     * @var array|null
     */
    private $map;

    public function __construct(
        StockCriteriaInterfaceFactory $stockCriteriaFactory,
        StockRepositoryInterface $stockRepository
    ) {
        $this->stockCriteriaFactory = $stockCriteriaFactory;
        $this->stockRepository = $stockRepository;
    }

    public function transform($value)
    {
        $map = $this->getMap();
        return $map[$value] ?? $value;
    }

    private function getMap()
    {
        if (!$this->map) {
            $this->map = [];

            /** @var StockCriteriaInterface $stockCriteria */
            $stockCriteria = $this->stockCriteriaFactory->create();
            $stockCollection = $this->stockRepository->getList($stockCriteria);
            foreach ($stockCollection->getItems() as $stock) {
                $this->map[$stock->getStockName()] = $stock->getStockId();
            }
        }
        return $this->map;
    }
}
