<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImport
 */

declare(strict_types=1);

namespace Amasty\ProductImport\Import\Action\Import\Product\ProductActions;

use Amasty\ImportCore\Api\Behavior\BehaviorResultInterface;
use Amasty\ImportCore\Api\ImportProcessInterface;
use Magento\Catalog\Model\Indexer\Category\Flat;
use Magento\Catalog\Model\Indexer\Category\Flat\State;
use Magento\Framework\Indexer\AbstractProcessor;

class Reindex extends AbstractAction
{
    /**
     * @var State
     */
    private $flatState;

    /**
     * @var Flat
     */
    private $categoryFlat;

    /**
     * @var AbstractProcessor[]
     */
    private $productIndexerPool;

    /**
     * @var AbstractProcessor[]
     */
    private $categoryIndexerPool;

    public function __construct(
        State $flatState,
        Flat $categoryFlat,
        array $productIndexerPool = [],
        array $categoryIndexerPool = []
    ) {
        $this->categoryFlat = $categoryFlat;
        $this->productIndexerPool = $productIndexerPool;
        $this->categoryIndexerPool = $categoryIndexerPool;
        $this->flatState = $flatState;
    }

    /**
     * @inheritdoc
     */
    public function execute(ImportProcessInterface $importProcess): void
    {
        $this->reindexProducts($importProcess);
        $this->reindexCategories($importProcess);
    }

    /**
     * Reindex product entities that were affected by import behavior
     *
     * @param ImportProcessInterface $importProcess
     * @return void
     */
    private function reindexProducts(ImportProcessInterface $importProcess): void
    {
        if (empty($this->productIndexerPool)) {
            return;
        }

        /** @var BehaviorResultInterface $productResult */
        $productResult = $importProcess->getProcessedEntityResult('catalog_product_entity');
        if (!$productResult) {
            return;
        }

        $affectedProductIds = $productResult->getAffectedIds();
        if (empty($affectedProductIds)) {
            return;
        }

        if (!$importProcess->isChildProcess() && $importProcess->getBatchNumber() == 1) {
            $importProcess->addInfoMessage(__('Reindex of catalog_product_entity is running.')->render());
        }

        foreach ($this->productIndexerPool as $indexer) {
            $indexer->reindexList($affectedProductIds, true);
        }

        // todo: Fix checking that the current batch is the last one BTS-9613
        if (!$importProcess->isHasNextBatch()) {
            $importProcess->addInfoMessage(__('Reindex of catalog_product_entity is complete.')->render());
        }
    }

    /**
     * Reindex category entities that were affected by import behavior
     *
     * @param ImportProcessInterface $importProcess
     * @return void
     */
    private function reindexCategories(ImportProcessInterface $importProcess): void
    {
        if (empty($this->categoryIndexerPool)) {
            return;
        }

        /** @var BehaviorResultInterface $productResult */
        $categoryResult = $importProcess->getProcessedEntityResult('catalog_category_entity');
        if (!$categoryResult) {
            return;
        }

        $affectedCategoryIds = $categoryResult->getAffectedIds();
        if (empty($affectedCategoryIds)) {
            return;
        }

        if (!$importProcess->isChildProcess() && $importProcess->getBatchNumber() == 1) {
            $importProcess->addInfoMessage(__('Reindex of catalog_category_entity is running.')->render());
        }

        foreach ($this->categoryIndexerPool as $indexer) {
            $indexer->reindexList($affectedCategoryIds, true);
        }
        if ($this->flatState->isFlatEnabled()) {
            $this->categoryFlat->execute($affectedCategoryIds);
        }

        // todo: Fix checking that the current batch is the last one BTS-9613
        if (!$importProcess->isHasNextBatch()) {
            $importProcess->addInfoMessage(__('Reindex of catalog_category_entity is complete.')->render());
        }
    }
}
