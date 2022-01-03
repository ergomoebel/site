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

class Region implements FieldValidatorInterface
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
            $region = trim($row[$field]);

            if (!empty($region)) {
                if (!isset($this->validationResult[$region])) {
                    $this->validationResult[$region] = $this->isRegionExists($region);
                }

                return $this->validationResult[$region];
            }
        }

        return true;
    }

    private function isRegionExists(string $region): bool
    {
        $regionTable = $this->connection->getTableName('directory_country_region');
        $connection = $this->connection->getConnection();

        return (bool)$connection->fetchOne(
            $connection->select()
                ->from($regionTable)
                ->where('region_id = ?', $region)
                ->limit(1)
                ->columns(['region_id'])
        );
    }
}
