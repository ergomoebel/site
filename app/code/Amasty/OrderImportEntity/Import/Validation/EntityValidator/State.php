<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderImportEntity
 */


declare(strict_types=1);

namespace Amasty\OrderImportEntity\Import\Validation\EntityValidator;

use Amasty\ImportCore\Api\Validation\FieldValidatorInterface;
use Magento\Framework\App\ResourceConnection;

class State implements FieldValidatorInterface
{
    /**
     * @var ResourceConnection
     */
    private $connection;

    /**
     * @var array
     */
    private $validationResult = [];

    public function __construct(ResourceConnection $connection)
    {
        $this->connection = $connection;
    }

    public function validate(array $row, string $field): bool
    {
        if (isset($row[$field])) {
            $state = trim($row[$field]);

            if (!empty($state)) {
                if (!isset($this->validationResult[$state])) {
                    $this->validationResult[$state] = $this->isStateExists($state);
                }

                return $this->validationResult[$state];
            }
        }

        return true;
    }

    private function isStateExists($state): bool
    {
        $stateTable = $this->connection->getTableName('sales_order_status_state');
        $connection = $this->connection->getConnection();

        return (bool)$connection->fetchOne(
            $connection->select()
                ->from($stateTable)
                ->where('state = ?', trim($state))
                ->limit(1)
                ->columns(['state'])
        );
    }
}
