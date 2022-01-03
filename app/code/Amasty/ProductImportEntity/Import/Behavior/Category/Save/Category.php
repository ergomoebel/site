<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\Behavior\Category\Save;

use Amasty\Base\Model\Serializer;
use Amasty\ImportCore\Api\Behavior\BehaviorResultInterface;
use Amasty\ImportCore\Api\Behavior\BehaviorResultInterfaceFactory;
use Amasty\ImportCore\Import\Behavior\AddUpdate\Table as TableBehavior;
use Amasty\ImportCore\Import\Utils\DuplicateFieldChecker;
use Amasty\ProductImportEntity\Import\DataHandling\SkuToProductId;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\ObjectManagerInterface;

class Category extends TableBehavior
{
    private $fieldMapping = [
        'entity_id' => 'category_id',
        'product_id' => 'product_id'
    ];

    const CATEGORY_PRODUCT_RELATION_TABLE = 'catalog_category_product';

    /**
     * @var SkuToProductId
     */
    private $skuToProductId;

    public function __construct(
        ObjectManagerInterface $objectManager,
        ResourceConnection $resourceConnection,
        Serializer $serializer,
        BehaviorResultInterfaceFactory $behaviorResultFactory,
        DuplicateFieldChecker $duplicateFieldChecker,
        SkuToProductId $skuToProductId,
        array $config
    ) {
        parent::__construct(
            $objectManager,
            $resourceConnection,
            $serializer,
            $behaviorResultFactory,
            $duplicateFieldChecker,
            $config
        );
        $this->skuToProductId = $skuToProductId;
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function execute(array &$data, ?string $customIdentifier = null): BehaviorResultInterface
    {
        $result = parent::execute($data, $customIdentifier);
        $this->saveCategoryProductRelations($data);

        return $result;
    }

    protected function saveCategoryProductRelations(array $data)
    {
        $result = [];

        $data = $this->skuToProductId->executeRows($data, 'product_id', 'product_sku');
        foreach ($data as $row) {
            $result[] = $this->getMappedRowData($row);
        }
        if (!empty($result)) {
            $this->getConnection()->insertOnDuplicate(
                $this->resourceConnection->getTableName(self::CATEGORY_PRODUCT_RELATION_TABLE),
                $result
            );
        }
    }

    protected function getMappedRowData(array $row): array
    {
        $result = [];
        foreach ($this->fieldMapping as $key => $value) {
            if (array_key_exists($key, $row)) {
                $result[$value] = $row[$key];
            }
        }

        return $result;
    }
}
