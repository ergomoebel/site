<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExportEntity
 */

declare(strict_types=1);

namespace Amasty\ProductExportEntity\Model\ResourceModel\CustomOption\Value;

use Amasty\ProductExportEntity\Export\CustomOption\ScopedEntity\OptionValueCollector;
use Amasty\ProductExportEntity\Model\ResourceModel\ScopedEntity\Collection\Field\ArgumentProcessor;
use Amasty\ProductExportEntity\Model\ResourceModel\ScopedEntityCollection;
use Magento\Catalog\Model\ResourceModel\Product\Option\Value\CollectionFactory as ValueCollectionFactory;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Store\Model\StoreManagerInterface;

class Collection extends ScopedEntityCollection
{
    /**
     * @var ValueCollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        EntityFactoryInterface $entityFactory,
        StoreManagerInterface $storeManager,
        OptionValueCollector $itemCollector,
        ValueCollectionFactory $collectionFactory,
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
        return OptionValueCollector::STORE_ID_FIELD;
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
