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
interface HistoryRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\ExportPro\Api\Data\HistoryInterface $history
     *
     * @return \Amasty\ExportPro\Api\Data\HistoryInterface
     */
    public function save(\Amasty\ExportPro\Api\Data\HistoryInterface $history);

    /**
     * Get by id
     *
     * @param int $id
     *
     * @return \Amasty\ExportPro\Api\Data\HistoryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * Delete
     *
     * @param \Amasty\ExportPro\Api\Data\HistoryInterface $exportHistory
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\ExportPro\Api\Data\HistoryInterface $exportHistory);

    /**
     * Delete by id
     *
     * @param int $id
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($id);

    /**
     * Clear history
     * @param string $type
     *
     * @return bool true on success
     * @throws \Exception
     */
    public function clearHistory(string $type);

    /**
     * Get by identity
     *
     * @param string $identity
     *
     * @return \Amasty\ExportPro\Api\Data\HistoryInterface
     */
    public function getByIdentity($identity);

    /**
     * @param string $type
     * @param int $days
     *
     * @return mixed
     */
    public function clearHistoryByDays(string $type, int $days);

    /**
     * @param string $type
     * @param int $days
     *
     * @return mixed
     */
    public function clearFilesByDays(string $type, int $days);

    /**
     * @param string $jobType
     * @param int $days
     *
     * @return \Amasty\ExportPro\Model\History\ResourceModel\Collection
     */
    public function getByDays(string $jobType, int $days);

    /**
     * @param string $jobType
     *
     * @return \Amasty\ExportPro\Model\History\ResourceModel\Collection
     */
    public function getByJobType(string $jobType);
}
