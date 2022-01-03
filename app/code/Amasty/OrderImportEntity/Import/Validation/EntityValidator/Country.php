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

class Country implements FieldValidatorInterface
{
    /**
     * @var ResourceConnection
     */
    private $connection;

    /**
     * @var array
     */
    private $validationResult;

    public function __construct(ResourceConnection $connection)
    {
        $this->connection = $connection;
    }

    public function validate(array $row, string $field): bool
    {
        if (isset($row[$field])) {
            $country = trim($row[$field]);

            if (!empty($country)) {
                if (!isset($this->validationResult[$country])) {
                    $this->validationResult[$country] = $this->isCountryExists($country);
                }

                return $this->validationResult[$country];
            }
        }

        return true;
    }

    private function isCountryExists(string $country): bool
    {
        $regionTable = $this->connection->getTableName('directory_country');
        $connection = $this->connection->getConnection();

        return (bool)$connection->fetchOne(
            $connection->select()
                ->from($regionTable)
                ->where('country_id = ?', $country)
                ->limit(1)
                ->columns(['country_id'])
        );
    }
}
