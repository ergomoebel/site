<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerExport
 */


declare(strict_types=1);

namespace Amasty\CustomerExport\Api;

use Amasty\CustomerExport\Api\Data\ProfileInterface;

interface ProfileRepositoryInterface
{
    /**
     * @param int $id
     * @return \Amasty\CustomerExport\Api\Data\ProfileInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $id): ProfileInterface;

    /**
     * @param \Amasty\CustomerExport\Api\Data\ProfileInterface $profile
     *
     * @return \Amasty\CustomerExport\Api\Data\ProfileInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(ProfileInterface $profile): ProfileInterface;

    /**
     * @param \Amasty\CustomerExport\Api\Data\ProfileInterface $profile
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(ProfileInterface $profile): bool;

    /**
     * @param int $id
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById(int $id): bool;

    /**
     * @param int $id
     *
     * @return \Amasty\CustomerExport\Api\Data\ProfileInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function updateLastExported(int $id): ProfileInterface;
}
