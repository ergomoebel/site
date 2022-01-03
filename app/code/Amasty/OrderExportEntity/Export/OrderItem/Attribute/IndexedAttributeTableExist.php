<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExportEntity
 */


namespace Amasty\OrderExportEntity\Export\OrderItem\Attribute;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Config\ConfigOptionsListConstants;

class IndexedAttributeTableExist
{
    /**
     * @var ResourceConnection
     */
    private $connection;

    /**
     * @var DeploymentConfig
     */
    private $deploymentConfig;

    public function __construct(
        ResourceConnection $connection,
        DeploymentConfig $deploymentConfig
    ) {
        $this->connection = $connection;
        $this->deploymentConfig = $deploymentConfig;
    }

    public function isEnabled(): bool
    {
        $connectionConfig = $this->deploymentConfig->get(
            ConfigOptionsListConstants::CONFIG_PATH_DB_CONNECTIONS . '/' . ResourceConnection::DEFAULT_CONNECTION
        );

        /**
         * Check for connection config on Magento Cloud platform while DI container rebuild
         */
        if (!$connectionConfig) {
            return false;
        }

        return (bool)$this->connection->getConnection()->isTableExists(
            $this->connection->getTableName('amasty_order_export_attribute_index')
        );
    }
}
