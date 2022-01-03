<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\Behavior\Product\Save\Website;

use Amasty\ImportCore\Api\Behavior\BehaviorResultInterface;
use Amasty\ImportCore\Api\Behavior\BehaviorResultInterfaceFactory;
use Amasty\ProductImportEntity\Import\Behavior\Product\AbstractDirectBehavior;
use Amasty\ProductImportEntity\Import\DataHandling\SkuToProductId;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;

class WebsiteProductRelation extends AbstractDirectBehavior
{
    const TABLE_NAME = 'catalog_product_website';

    /**
     * @var SkuToProductId
     */
    private $skuToProductId;

    public function __construct(
        ResourceConnection $resourceConnection,
        StoreManagerInterface $storeManager,
        BehaviorResultInterfaceFactory $resultFactory,
        SkuToProductId $skuToProductId
    ) {
        parent::__construct(
            $resourceConnection,
            $storeManager,
            $resultFactory
        );
        $this->skuToProductId = $skuToProductId;
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function execute(array &$data, ?string $customIdentifier = null): BehaviorResultInterface
    {
        $result = $this->resultFactory->create();
        $data = $this->skuToProductId->executeRows($data, 'product_id', 'product_sku');

        $tableName = $this->getTableName(self::TABLE_NAME);
        $preparedData = $this->prepareDataForTable($data, $tableName);
        $this->getConnection()->insertOnDuplicate(
            $tableName,
            $preparedData
        );

        return $result;
    }
}
