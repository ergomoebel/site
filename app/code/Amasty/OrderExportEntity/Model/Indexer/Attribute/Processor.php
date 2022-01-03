<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExportEntity
 */

declare(strict_types=1);

namespace Amasty\OrderExportEntity\Model\Indexer\Attribute;

use Amasty\OrderExportEntity\Model\Indexer\Attribute as AttributeIndexer;

class Processor extends \Magento\Framework\Indexer\AbstractProcessor
{
    /**
     * {@inheritdoc}
     */
    const INDEXER_ID = AttributeIndexer::INDEXER_ID;
}
