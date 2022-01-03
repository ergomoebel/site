<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


declare(strict_types=1);

namespace Amasty\OrderExport\Model\Connection;

use Amasty\OrderExport\Api\Data\ConnectionInterface;
use Magento\Framework\Model\AbstractModel;

class Connection extends AbstractModel implements ConnectionInterface
{
    const ID = 'id';
    const NAME = 'name';
    const PARENT_ENTITY = 'parent_entity';
    const ENTITY_CODE = 'entity_code';
    const TABLE_TO_JOIN = 'table_to_join';
    const BASE_TABLE_KEY = 'base_table_key';
    const REFERENCED_TABLE_KEY = 'referenced_table_key';
    const VIRTUAL_PREFIX = 'virtual_';

    public function _construct()
    {
        parent::_construct();
        $this->_init(ResourceModel\Connection::class);
        $this->setIdFieldName(self::ID);
    }

    public function getConnectionId(): ?int
    {
        return (int)$this->getData(self::ID);
    }

    public function setConnectionId(?int $id): ConnectionInterface
    {
        $this->setData(self::NAME, $id);

        return $this;
    }

    public function getName(): ?string
    {
        return $this->getData(self::NAME);
    }

    public function setName(?string $name): ConnectionInterface
    {
        $this->setData(self::NAME, $name);

        return $this;
    }

    public function getTableToJoin(): ?string
    {
        return $this->getData(self::TABLE_TO_JOIN);
    }

    public function setTableToJoin(?string $table): ConnectionInterface
    {
        $this->setData(self::TABLE_TO_JOIN, $table);

        return $this;
    }

    public function getParentEntity(): ?string
    {
        return $this->getData(self::PARENT_ENTITY);
    }

    public function setParentEntity(?string $parentEntity): ConnectionInterface
    {
        $this->setData(self::PARENT_ENTITY, $parentEntity);

        return $this;
    }

    public function getEntityCode(): ?string
    {
        return $this->getData(self::ENTITY_CODE);
    }

    public function setEntityCode(?string $entityCode): ConnectionInterface
    {
        $this->setData(self::ENTITY_CODE, $entityCode);

        return $this;
    }

    public function getBaseTableKey(): ?string
    {
        return $this->getData(self::BASE_TABLE_KEY);
    }

    public function setBaseTableKey(?string $baseTableKey): ConnectionInterface
    {
        $this->setData(self::BASE_TABLE_KEY, $baseTableKey);

        return $this;
    }

    public function getReferencedTableKey(): ?string
    {
        return $this->getData(self::REFERENCED_TABLE_KEY);
    }

    public function setReferencedTableKey(?string $referencedTableKey): ConnectionInterface
    {
        $this->setData(self::REFERENCED_TABLE_KEY, $referencedTableKey);

        return $this;
    }
}
