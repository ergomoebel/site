<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


declare(strict_types=1);

namespace Amasty\OrderExport\Model\Connection;

use Amasty\OrderExport\Api\ConnectionRepositoryInterface;
use Amasty\OrderExport\Api\Data\ConnectionInterface;
use Amasty\OrderExport\Api\Data\ConnectionInterfaceFactory;
use Amasty\OrderExport\Model\Connection\ResourceModel\CollectionFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class Repository implements ConnectionRepositoryInterface
{
    /**
     * @var ConnectionInterfaceFactory
     */
    private $connectionFactory;

    /**
     * @var ResourceModel\Connection
     */
    private $connectionResource;

    private $connections = [];

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        ConnectionInterfaceFactory $connectionFactory,
        ResourceModel\Connection $connectionResource,
        CollectionFactory $collectionFactory
    ) {
        $this->connectionFactory = $connectionFactory;
        $this->connectionResource = $connectionResource;
        $this->collectionFactory = $collectionFactory;
    }

    public function getById(int $id): ConnectionInterface
    {
        if (!isset($this->connections[$id])) {
            /** @var ConnectionInterface $connection */
            $connection = $this->connectionFactory->create();
            $this->connectionResource->load($connection, $id);
            if (!$connection->getConnectionId()) {
                throw new NoSuchEntityException(__('Connection with specified ID "%1" not found.', $id));
            }
            $this->connections[$id] = $connection;
        }

        return $this->connections[$id];
    }

    public function save(ConnectionInterface $connection): ConnectionInterface
    {
        try {
            $resourceConnection = $this->connectionResource->getConnection();
            $referencedTableName = $this->connectionResource->getTable($connection->getTableToJoin());
            if (!$resourceConnection->isTableExists($referencedTableName)) {
                throw new CouldNotSaveException(__('Database table with specified name does not exist.'));
            }
            if (!$resourceConnection->tableColumnExists($referencedTableName, $connection->getReferencedTableKey())) {
                throw new CouldNotSaveException(
                    __('Column with specified name does not exist in the specified database table.')
                );
            }
            if ($this->getByTableAndParent(
                $connection->getTableToJoin(),
                $connection->getParentEntity()
            )->getConnectionId() && $connection->isObjectNew()
            ) {
                throw new CouldNotSaveException(
                    __('Connection with table to join %1 already exists.', $connection->getTableToJoin())
                );
            }

            $connection->setEntityCode(uniqid());
            $this->connectionResource->save($connection);

            unset($this->connections[$connection->getConnectionId()]);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Unable to save the connection. Error: %1', $e->getMessage()));
        }

        return $connection;
    }

    public function delete(ConnectionInterface $connection): bool
    {
        try {
            $this->connectionResource->delete($connection);
            unset($this->connections[$connection->getConnectionId()]);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Unable to delete the connection. Error: %1', $e->getMessage()));
        }

        return true;
    }

    public function deleteById(int $id): bool
    {
        $connection = $this->getById($id);

        return $this->delete($connection);
    }

    public function getEmptyConnectionModel(): ConnectionInterface
    {
        return $this->connectionFactory->create();
    }

    public function getByTableAndParent(?string $tableToJoin, ?string $parentEntity): ConnectionInterface
    {
        /** @var ConnectionInterface $connection */
        $connection = $this->collectionFactory->create()
            ->addFieldToFilter(Connection::TABLE_TO_JOIN, $tableToJoin)
            ->addFieldToFilter(Connection::PARENT_ENTITY, $parentEntity)
            ->getFirstItem();

        return $connection;
    }
}
