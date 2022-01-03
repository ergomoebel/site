<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerImport
 */


declare(strict_types=1);

namespace Amasty\CustomerImport\Api;

use Amasty\CustomerImport\Api\Data\ProfileInterface;

interface ProfileRepositoryInterface
{
    /**
     * @param int $id
     * @return \Amasty\CustomerImport\Api\Data\ProfileInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $id): ProfileInterface;

    /**
     * @param \Amasty\CustomerImport\Api\Data\ProfileInterface $profile
     *
     * @return \Amasty\CustomerImport\Api\Data\ProfileInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(ProfileInterface $profile): ProfileInterface;

    /**
     * @param \Amasty\CustomerImport\Api\Data\ProfileInterface $profile
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
     * @return \Amasty\CustomerImport\Api\Data\ProfileInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function updateLastImported(int $id): ProfileInterface;
}
