<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\FieldsClass\Product;

use Amasty\ImportCore\Api\Config\Entity\FieldsConfigInterface;
use Amasty\ImportCore\Import\FieldsClass\Describe as CoreDescribe;

class Describe extends CoreDescribe
{
    /**
     * @inheritDoc
     */
    public function execute(FieldsConfigInterface $existingConfig): FieldsConfigInterface
    {
        $fieldsConfig = parent::execute($existingConfig);

        $fields = $fieldsConfig->getFields();
        $rowIdField = $this->getFieldByName('row_id', $fields);
        if ($rowIdField) {
            $entityIdField = $this->getFieldByName('entity_id', $fields);
            $entityIdField->setIdentification(null);
        }

        return $fieldsConfig;
    }
}
