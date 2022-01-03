<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


namespace Amasty\ExportPro\Api;

/**
 * @api
 */
interface CronJobRepositoryInterface
{
    /**
     * @param \Amasty\ExportPro\Api\Data\CronJobInterface $cronJob
     *
     * @return \Amasty\ExportPro\Api\Data\CronJobInterface
     */
    public function save(\Amasty\ExportPro\Api\Data\CronJobInterface $cronJob);

    /**
     * @param int $id
     *
     * @return \Amasty\ExportPro\Api\Data\CronJobInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * @param \Amasty\ExportPro\Api\Data\CronJobInterface $cronJob
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\ExportPro\Api\Data\CronJobInterface $cronJob);

    /**
     * @param int $id
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($id);
}
