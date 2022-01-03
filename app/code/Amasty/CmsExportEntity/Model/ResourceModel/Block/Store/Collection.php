<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CmsExportEntity
 */

declare(strict_types=1);

namespace Amasty\CmsExportEntity\Model\ResourceModel\Block\Store;

use Amasty\CmsExportEntity\Model\ResourceModel\AbstractStoreCollection;
use Amasty\CmsExportEntity\Model\Block\Store as BlockStore;
use Amasty\CmsExportEntity\Model\ResourceModel\Block\Store as BlockStoreResource;

class Collection extends AbstractStoreCollection
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(BlockStore::class, BlockStoreResource::class);
    }
}
