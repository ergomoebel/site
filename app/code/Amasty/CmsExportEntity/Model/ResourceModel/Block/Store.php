<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CmsExportEntity
 */

declare(strict_types=1);

namespace Amasty\CmsExportEntity\Model\ResourceModel\Block;

use Magento\Cms\Api\Data\BlockInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class Store extends AbstractDb
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    public function __construct(
        Context $context,
        MetadataPool $metadataPool,
        ?string $connectionName = null
    ) {
        $this->metadataPool = $metadataPool;
        parent::__construct($context, $connectionName);
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $metadata = $this->metadataPool->getMetadata(BlockInterface::class);
        $this->_init('cms_block_store', $metadata->getLinkField());
    }

    /**
     * @inheritDoc
     */
    public function getConnection()
    {
        $metadata = $this->metadataPool->getMetadata(BlockInterface::class);

        return $this->_resources->getConnectionByName($metadata->getEntityConnectionName());
    }
}
