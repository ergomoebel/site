<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_UrlRewriteImportEntity
 */


declare(strict_types=1);

namespace Amasty\UrlRewriteImportEntity\Import\DataHandling\RowModifier\UrlRewrite;

use Amasty\Base\Model\Serializer;
use Amasty\ImportCore\Api\Modifier\RowModifierInterface;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;

class Product implements RowModifierInterface
{
    const PRODUCT_ID_FIELD_NAME = 'product_sku';
    const CATEGORY_ID_FIELD_NAME = 'categories';

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @inheritDoc
     */
    public function transform(array &$row): array
    {
        $row['entity_type'] = ProductUrlRewriteGenerator::ENTITY_TYPE;

        if (isset($row[self::PRODUCT_ID_FIELD_NAME])) {
            $row['entity_id'] = $row[self::PRODUCT_ID_FIELD_NAME];

            $this->adjustTargetPath(
                $row,
                $row[self::PRODUCT_ID_FIELD_NAME],
                $row[self::CATEGORY_ID_FIELD_NAME] ?? null
            );
            if (isset($row[self::CATEGORY_ID_FIELD_NAME])) {
                $this->adjustMetadata($row, $row[self::CATEGORY_ID_FIELD_NAME]);
            }
        }

        return $row;
    }

    /**
     * Adjusts target_path value: set actual entity Ids
     *
     * @param array $row
     * @param int $productId
     * @param string|null $categoryId
     * @return array
     */
    private function adjustTargetPath(array &$row, $productId, $categoryId = null): array
    {
        $targetPath = 'catalog/product/view/id/' . $productId;
        if ($categoryId) {
            $targetPath .= '/category/' . $categoryId;
        }

        $row['target_path'] = $targetPath;

        return $row;
    }

    /**
     * Adjust metadata value: set actual category Id
     *
     * @param array $row
     * @param int $categoryId
     * @return array
     */
    private function adjustMetadata(array &$row, $categoryId): array
    {
        if (!empty($row['metadata'])) {
            $metadata = $this->serializer->unserialize($row['metadata']);
            if (isset($metadata['category_id'])) {
                $metadata['category_id'] = $categoryId;
            }

            $row['metadata'] = $this->serializer->serialize($metadata);
        }

        return $row;
    }
}
