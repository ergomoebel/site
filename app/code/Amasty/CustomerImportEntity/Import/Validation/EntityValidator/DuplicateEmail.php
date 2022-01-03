<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerImportEntity
 */

declare(strict_types=1);

namespace Amasty\CustomerImportEntity\Import\Validation\EntityValidator;

use Amasty\ImportCore\Api\Validation\FieldValidatorInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;

class DuplicateEmail implements FieldValidatorInterface
{
    const TARGET_FIELDS = [
        'email',
        'website_id'
    ];

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var string
     */
    private $idField;

    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    public function validate(array $row, string $field): bool
    {
        return !(bool)$this->getDuplicateRowId($row);
    }

    private function getDuplicateRowId(array $row)
    {
        $connection = $this->getConnection();
        $fullTableName = $this->resourceConnection->getTableName('customer_entity');
        if (!$connection->isTableExists($fullTableName)) {
            return false;
        }

        $select = $connection->select()->from($fullTableName);
        $andParts = [];
        foreach (self::TARGET_FIELDS as $field) {
            if (empty($row[$field])) {
                return false;
            }
            $andParts[] = $connection->quoteInto($field . ' = ?', $row[$field]);
        }
        $select->orWhere(implode(' AND ', $andParts));

        $idField = $this->getIdField($fullTableName);
        if ($idField && !empty($row[$idField])) {
            $select->where($connection->quoteInto($idField . ' != ?', $row[$idField]));
        }

        return $connection->fetchOne($select);
    }

    private function getConnection(): AdapterInterface
    {
        return $this->resourceConnection->getConnection();
    }

    private function getIdField(string $fullTableName): string
    {
        if ($this->idField === null) {
            $this->idField = $this->getConnection()->getAutoIncrementField($fullTableName) ?: '';
        }

        return $this->idField;
    }
}
