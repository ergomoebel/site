<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExportEntity
 */

declare(strict_types=1);

namespace Amasty\OrderExportEntity\Export\OrderAttribute;

use Amasty\ExportCore\Api\CollectionModifierInterface;

class CollectionModifier implements CollectionModifierInterface
{
    public function apply(\Magento\Framework\Data\Collection $collection): CollectionModifierInterface
    {
        $collection->addFieldToFilter('parent_entity_type', 1);

        return $this;
    }
}
