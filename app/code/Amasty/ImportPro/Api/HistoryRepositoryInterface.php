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
interface HistoryRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\ImportPro\Api\Data\HistoryInterface $history
     *
     * @return \Amasty\ImportPro\Api\Data\HistoryInterface
     */
    public function save(\Amasty\ImportPro\Api\Data\HistoryInterface $history);

    /**
     * Get by id
     *
     * @param int $id
     *
     * @return \Amasty\ImportPro\Api\Data\HistoryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * Delete
     *
     * @param \Amasty\ImportPro\Api\Data\HistoryInterface $history
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\ImportPro\Api\Data\HistoryInterface $history);

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
     *
     * @return bool true on success
     * @throws \Exception
     */
    public function clearHistory();

    /**
     * Get by identity
     *
     * @param string $identity
     *
     * @return \Amasty\ImportPro\Api\Data\HistoryInterface
     */
    public function getByIdentity($identity);

    /**
     * @param string $jobType
     * @param int $days
     *
     * @return void
     */
    public function clearHistoryByDays(string $jobType, int $days);
}
