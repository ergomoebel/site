<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductExportEntity\Export\DataHandling\FieldModifier\CatalogInventory;

use Amasty\ExportCore\Api\FieldModifier\FieldModifierInterface;
use Amasty\ExportCore\Export\DataHandling\AbstractModifier;
use Magento\CatalogInventory\Api\StockCriteriaInterface;
use Magento\CatalogInventory\Api\StockCriteriaInterfaceFactory;
use Magento\CatalogInventory\Api\StockRepositoryInterface;

class StockId2StockName extends AbstractModifier implements FieldModifierInterface
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
        StockRepositoryInterface $stockRepository,
        $config
    ) {
        parent::__construct($config);
        $this->stockCriteriaFactory = $stockCriteriaFactory;
        $this->stockRepository = $stockRepository;
    }

    public function transform($value)
    {
        $map = $this->getMap();
        return $map[$value] ?? $value;
    }

    /**
     * Get option value to option label map
     *
     * @return array
     */
    private function getMap()
    {
        if (!$this->map) {
            $this->map = [];

            /** @var StockCriteriaInterface $stockCriteria */
            $stockCriteria = $this->stockCriteriaFactory->create();
            $stockCollection = $this->stockRepository->getList($stockCriteria);
            foreach ($stockCollection->getItems() as $stock) {
                $this->map[$stock->getStockId()] = $stock->getStockName();
            }
        }
        return $this->map;
    }

    public function getLabel(): string
    {
        return __('Stock Id To Stock Name')->getText();
    }
}
