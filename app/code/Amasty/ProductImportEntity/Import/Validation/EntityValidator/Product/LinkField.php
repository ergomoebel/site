<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */

declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\Validation\EntityValidator\Product;

use Amasty\ImportCore\Api\Validation\FieldValidatorInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;

class LinkField implements FieldValidatorInterface
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var array
     */
    private $validationResult;

    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @inheritDoc
     */
    public function validate(array $row, string $field): bool
    {
        if (isset($row[$field])) {
            $value = trim($row[$field]);

            if (!empty($value)) {
                if (!isset($this->validationResult[$value])) {
                    $this->validationResult[$value] = $this->isProductExists($value);
                }

                return $this->validationResult[$value];
            }
        }

        return true;
    }

    /**
     * Checks if product with specified link field value exists
     *
     * @param int $linkField
     * @return bool
     * @throws \Exception
     */
    private function isProductExists($linkField): bool
    {
        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('catalog_product_entity');

        $metadata = $this->metadataPool->getMetadata(ProductInterface::class);

        return (bool)$connection->fetchOne(
            $connection->select()
                ->from($tableName)
                ->where($metadata->getLinkField() . ' = ?', $linkField)
                ->limit(1)
                ->columns(['entity_id'])
        );
    }
}
