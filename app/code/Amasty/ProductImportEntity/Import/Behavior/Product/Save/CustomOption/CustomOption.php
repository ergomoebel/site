<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\Behavior\Product\Save\CustomOption;

use Amasty\ImportCore\Import\Source\SourceDataStructure;

class CustomOption extends AbstractCustomOption
{
    const CUSTOM_OPTION_VALUE_ENTITY_KEY = 'catalog_product_custom_option_value';

    /**
     * @inheritDoc
     */
    protected $linkedTableToFieldsMap = [
        'catalog_product_option_title' => ['title'],
        'catalog_product_option_price' => [
            'price',
            'price_type'
        ]
    ];

    /**
     * @inheritDoc
     */
    protected $identityKeys = ['option_id', 'product_id'];

    /**
     * @inheritDoc
     */
    protected function getMainTable()
    {
        return 'catalog_product_option';
    }

    /**
     * @inheritDoc
     */
    protected function registerSubEntities(array $data)
    {
        foreach ($data as $row) {
            $subEntitiesData = $row[SourceDataStructure::SUB_ENTITIES_DATA_KEY]
                [self::CUSTOM_OPTION_VALUE_ENTITY_KEY] ?? [];
            if (empty($subEntitiesData)) {
                continue;
            }

            foreach ($subEntitiesData as $subEntityRow) {
                $subIdentity = $this->getIdentityKey($subEntityRow, ['option_id']);
                $this->identityRegistry->add($subIdentity);
            }
        }
    }
}
