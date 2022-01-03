<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\Validation\RowValidator\Inventory;

use Amasty\ImportCore\Api\Validation\RowValidatorInterface;
use Amasty\ProductImportEntity\Model\ResourceModel\CompositePKeyValidator;
use Magento\Framework\App\ResourceConnection;

class SourceItemValidator implements RowValidatorInterface
{
    /**
     * @var string|null
     */
    private $message;

    /**
     * @var CompositePKeyValidator
     */
    private $compositePKeyValidator;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(
        CompositePKeyValidator $compositePKeyValidator,
        ResourceConnection $resourceConnection
    ) {
        $this->compositePKeyValidator = $compositePKeyValidator;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @inheritDoc
     */
    public function validate(array $row): bool
    {
        $this->message = null;
        $sourceItemId = (int)($row['source_item_id'] ?? 0);

        /**
         * In case of Source Item ID is exists we must avoid unique key validation because
         * of using insertOnDuplicate() in custom Inventory save behavior.
         * @see \Amasty\ProductImportEntity\Import\Behavior\Inventory\Save\AbstractInventory
         */
        if ($sourceItemId && $this->isSourceItemExists($sourceItemId)) {
            return true;
        }

        if (isset($row['source_code']) && isset($row['sku'])) {
            $isPKeyDuplicated = $this->compositePKeyValidator->isUniquePartDuplicated(
                $row,
                ['source_code', 'sku'],
                ['source_code', 'sku'],
                'inventory_source_item'
            );
            if ($isPKeyDuplicated) {
                $this->message = (string)__(
                    'Inventory Source Item with sku "%1" is already exists on "%2" source',
                    $row['sku'],
                    $row['source_code']
                );

                return false;
            }
        }

        return true;
    }

    private function isSourceItemExists(int $sourceItemId): bool
    {
        $select = $this->resourceConnection->getConnection()->select()
            ->from($this->resourceConnection->getTableName('inventory_source_item'))
            ->where('source_item_id = ?', $sourceItemId)
            ->limit(1);

        return (bool)$this->resourceConnection->getConnection()->fetchOne($select);
    }

    /**
     * @inheritDoc
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }
}
