<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExportEntity
 */

declare(strict_types=1);

namespace Amasty\ProductExportEntity\Model\ResourceModel\Product\Type\Bundle\Option\Value;

use Amasty\ProductExportEntity\Export\Product\Type\Bundle\ScopedEntity\Option\ValueCollector;
use Amasty\ProductExportEntity\Model\ResourceModel\ScopedEntity\Collection\Field\ArgumentProcessor;
use Amasty\ProductExportEntity\Model\ResourceModel\ScopedEntityCollection;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Store\Model\StoreManagerInterface;

class ScopedCollection extends ScopedEntityCollection
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        EntityFactoryInterface $entityFactory,
        StoreManagerInterface $storeManager,
        ValueCollector $itemCollector,
        CollectionFactory $collectionFactory,
        ArgumentProcessor $fieldArgumentProcessor
    ) {
        parent::__construct(
            $entityFactory,
            $storeManager,
            $itemCollector,
            $fieldArgumentProcessor
        );
        $this->collectionFactory = $collectionFactory;
    }

    protected function getScopeFieldName()
    {
        return ValueCollector::STORE_ID_FIELD;
    }

    protected function getCollectionFactory()
    {
        return $this->collectionFactory;
    }

    protected function getRedundantFields()
    {
        $redundantFields = parent::getRedundantFields();
        $redundantFields[] = 'option_id';
        return $redundantFields;
    }
}
