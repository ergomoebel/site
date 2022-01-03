<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExportEntity
 */

declare(strict_types=1);

namespace Amasty\ProductExportEntity\Model\ResourceModel\CategoryAttribute;

use Amasty\ProductExportEntity\Export\Category\ScopedEntity\CategoryCollector;
use Amasty\ProductExportEntity\Model\ResourceModel\ScopedEntity\Collection\Field\ArgumentProcessor;
use Amasty\ProductExportEntity\Model\ResourceModel\ScopedEntityCollection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Store\Model\StoreManagerInterface;

class Collection extends ScopedEntityCollection
{
    /**
     * @var CategoryCollectionFactory
     */
    private $collectionFactory;

    private $idFieldName = null;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(
        EntityFactoryInterface $entityFactory,
        StoreManagerInterface $storeManager,
        CategoryCollector $itemCollector,
        CategoryCollectionFactory $collectionFactory,
        ArgumentProcessor $fieldArgumentProcessor,
        ResourceConnection $resourceConnection
    ) {
        parent::__construct(
            $entityFactory,
            $storeManager,
            $itemCollector,
            $fieldArgumentProcessor
        );
        $this->collectionFactory = $collectionFactory;
        $this->resourceConnection = $resourceConnection;
    }

    public function getIdFieldName()
    {
        if (!$this->idFieldName) {
            $this->idFieldName = $this->getCollectionFactory()->create()->getEntity()->getLinkField();
        }

        return $this->idFieldName;
    }

    protected function getScopeFieldName()
    {
        return CategoryCollector::STORE_ID_FIELD;
    }

    protected function getCollectionFactory()
    {
        return $this->collectionFactory;
    }

    public function getMainTable()
    {
        return $this->resourceConnection->getTableName('catalog_category_entity');
    }
}
