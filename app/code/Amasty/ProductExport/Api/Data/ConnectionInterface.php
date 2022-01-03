<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExport
 */


declare(strict_types=1);

namespace Amasty\ProductExport\Api\Data;

interface ConnectionInterface
{
    /**
     * @return int|null
     */
    public function getConnectionId(): ?int;

    /**
     * @param int|null $id
     *
     * @return \Amasty\ProductExport\Api\Data\ConnectionInterface
     */
    public function setConnectionId(?int $id): ConnectionInterface;

    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @param string|null $name
     *
     * @return \Amasty\ProductExport\Api\Data\ConnectionInterface
     */
    public function setName(?string $name): ConnectionInterface;

    /**
     * @return string|null
     */
    public function getTableToJoin(): ?string;

    /**
     * @param string|null $table
     *
     * @return \Amasty\ProductExport\Api\Data\ConnectionInterface
     */
    public function setTableToJoin(?string $table): ConnectionInterface;

    /**
     * @return string|null
     */
    public function getBaseTableKey(): ?string;

    /**
     * @param string|null $baseTableKey
     *
     * @return \Amasty\ProductExport\Api\Data\ConnectionInterface
     */
    public function setBaseTableKey(?string $baseTableKey): ConnectionInterface;

    /**
     * @return string|null
     *
     * @return \Amasty\ProductExport\Api\Data\ConnectionInterface
     */
    public function getParentEntity(): ?string;

    /**
     * @param string|null $parentEntity
     *
     * @return \Amasty\ProductExport\Api\Data\ConnectionInterface
     */
    public function setParentEntity(?string $parentEntity): ConnectionInterface;

    /**
     * @return string|null
     */
    public function getEntityCode(): ?string;

    /**
     * @param string|null $entityCode
     *
     * @return \Amasty\ProductExport\Api\Data\ConnectionInterface
     */
    public function setEntityCode(?string $entityCode): ConnectionInterface;

    /**
     * @return string|null
     */
    public function getReferencedTableKey(): ?string;

    /**
     * @param string|null $referencedTableKey
     *
     * @return \Amasty\ProductExport\Api\Data\ConnectionInterface
     */
    public function setReferencedTableKey(?string $referencedTableKey): ConnectionInterface;
}
