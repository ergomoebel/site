<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_UrlRewriteImportEntity
 */


declare(strict_types=1);

namespace Amasty\UrlRewriteImportEntity\Import\DataHandling\RowModifier\UrlRewrite;

use Amasty\ImportCore\Api\Modifier\RowModifierInterface;
use Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGenerator;

class Category implements RowModifierInterface
{
    const CATEGORY_ID_FIELD_NAME = 'categories';

    /**
     * @inheritDoc
     */
    public function transform(array &$row): array
    {
        $row['entity_type'] = CategoryUrlRewriteGenerator::ENTITY_TYPE;

        if (isset($row[self::CATEGORY_ID_FIELD_NAME])) {
            $row['entity_id'] = $row[self::CATEGORY_ID_FIELD_NAME];
            $row['target_path'] = 'catalog/category/view/id/' . $row[self::CATEGORY_ID_FIELD_NAME];
        }

        return $row;
    }
}
