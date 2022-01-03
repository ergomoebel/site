<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerExport
 */


declare(strict_types=1);

namespace Amasty\CustomerExport\Api;

use Amasty\CustomerExport\Api\Data\ConnectionInterface;

interface ConnectionRepositoryInterface
{
    /**
     * @param int $id
     *
     * @return \Amasty\CustomerExport\Api\Data\ConnectionInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $id): ConnectionInterface;

    /**
     * @param \Amasty\CustomerExport\Api\Data\ConnectionInterface $connection
     *
     * @return \Amasty\CustomerExport\Api\Data\ConnectionInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(ConnectionInterface $connection): ConnectionInterface;

    /**
     * @param \Amasty\CustomerExport\Api\Data\ConnectionInterface $connection
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(ConnectionInterface $connection): bool;

    /**
     * @param int $id
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById(int $id): bool;
}
