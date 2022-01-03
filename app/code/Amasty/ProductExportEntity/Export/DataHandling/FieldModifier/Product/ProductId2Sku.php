<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExportEntity
 */

declare(strict_types=1);

namespace Amasty\ProductExportEntity\Export\DataHandling\FieldModifier\Product;

use Amasty\ExportCore\Api\FieldModifier\FieldModifierInterface;
use Amasty\ExportCore\Export\DataHandling\AbstractModifier;
use Amasty\ExportCore\Export\DataHandling\ModifierProvider;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\EntityManager\MetadataPool;

class ProductId2Sku extends AbstractModifier implements FieldModifierInterface
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection,
        $config
    ) {
        parent::__construct($config);
        $this->metadataPool = $metadataPool;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @inheritDoc
     */
    public function transform($value)
    {
        if (!empty($value)) {
            $sku = $this->getSkuByEntityId($value);
            if ($sku) {
                return $sku;
            }
        }

        return $value;
    }

    /**
     * Get product sku by entity_id
     *
     * @param int $entityId
     * @return string
     * @throws \Exception
     */
    private function getSkuByEntityId($entityId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->resourceConnection->getTableName('catalog_product_entity'), 'sku')
            ->where('entity_id = ?', $entityId);

        return $connection->fetchOne($select);
    }

    /**
     * Get product entity connection
     *
     * @return AdapterInterface
     * @throws \Exception
     */
    private function getConnection()
    {
        $metadata = $this->metadataPool->getMetadata(ProductInterface::class);
        return $this->resourceConnection->getConnection(
            $metadata->getEntityConnectionName()
        );
    }

    /**
     * @inheritDoc
     */
    public function getLabel(): string
    {
        return __('Product Id To SKU')->getText();
    }

    /**
     * @inheritDoc
     */
    public function getGroup(): string
    {
        return ModifierProvider::CUSTOM_GROUP;
    }
}
