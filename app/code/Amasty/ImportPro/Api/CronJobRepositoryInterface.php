<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */


namespace Amasty\ImportPro\Api;

/**
 * @api
 */
interface CronJobRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\ImportPro\Api\Data\CronJobInterface $cronJob
     *
     * @return \Amasty\ImportPro\Api\Data\CronJobInterface
     */
    public function save(\Amasty\ImportPro\Api\Data\CronJobInterface $cronJob);

    /**
     * Get by id
     *
     * @param int $id
     *
     * @return \Amasty\ImportPro\Api\Data\CronJobInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * Delete
     *
     * @param \Amasty\ImportPro\Api\Data\CronJobInterface $cronJob
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\ImportPro\Api\Data\CronJobInterface $cronJob);

    /**
     * Delete by id
     *
     * @param int $id
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($id);
}
