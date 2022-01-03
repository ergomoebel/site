<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */


declare(strict_types=1);

namespace Amasty\ImportPro\Model\History\ResourceModel;

use Amasty\ImportPro\Model\History\History as HistoryModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class History extends AbstractDb
{
    const TABLE_NAME = 'amasty_import_history';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, HistoryModel::HISTORY_ID);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function clearTable()
    {
        $this->getConnection()->truncateTable($this->getMainTable());
    }

    public function clearHistoryByDate(string $jobType, string $dateToDelete)
    {
        $where = [
            HistoryModel::TYPE . ' = ?' => $jobType,
            HistoryModel::FINISHED_AT . ' < ?' => $dateToDelete
        ];
        $this->getConnection()->delete($this->getMainTable(), $where);
    }
}
