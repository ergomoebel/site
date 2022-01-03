<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\Behavior\Product\Save\CustomOption;

class CustomOptionValue extends AbstractCustomOption
{
    /**
     * @inheritDoc
     */
    protected $linkedTableToFieldsMap = [
        'catalog_product_option_type_title' => ['title'],
        'catalog_product_option_type_price' => [
            'price',
            'price_type'
        ]
    ];

    /**
     * @inheritDoc
     */
    protected $identityKeys = ['option_type_id', 'option_id'];

    /**
     * @inheritDoc
     */
    protected function getMainTable()
    {
        return 'catalog_product_option_type_value';
    }

    /**
     * @inheritDoc
     */
    protected function isNestedDataRowPersisted(array $row)
    {
        $identity = $this->getIdentityKey($row, ['option_id']);

        return $this->identityRegistry->isPersisted($identity);
    }

    /**
     * @inheritDoc
     */
    protected function markRowsPersisted(array $data)
    {
        foreach ($data as $row) {
            $identity = $this->getIdentityKey($row, ['option_id']);
            $this->identityRegistry->markPersisted($identity);
        }
    }
}
