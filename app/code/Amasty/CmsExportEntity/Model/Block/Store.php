<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CmsExportEntity
 */

declare(strict_types=1);

namespace Amasty\CmsExportEntity\Model\Block;

use Amasty\CmsExportEntity\Model\ResourceModel\Block\Store as BlockStore;
use Magento\Framework\Model\AbstractModel;

class Store extends AbstractModel
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(BlockStore::class);
    }
}
