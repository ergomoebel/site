<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerImportEntity
 */

declare(strict_types=1);

namespace Amasty\CustomerImportEntity\Model\ResourceModel\Customer;

use Magento\Customer\Model\Customer;
use Magento\Framework\Indexer\IndexerInterface;
use Magento\Framework\Indexer\IndexerRegistry;

class GridIndexer
{
    /**
     * @var IndexerInterface
     */
    private $indexer;

    public function __construct(
        IndexerRegistry $indexerRegistry
    ) {
        $this->indexer = $indexerRegistry->get(Customer::CUSTOMER_GRID_INDEXER_ID);
    }

    public function update($value): void
    {
        $this->indexer->reindexList($value);
    }
}
