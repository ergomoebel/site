<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Model\History\ResourceModel;

use Amasty\ExportPro\Model\History\History as HistoryModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class History extends AbstractDb
{
    const TABLE_NAME = 'amasty_export_history';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, HistoryModel::HISTORY_ID);
    }

    /**
     * @param string $type
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function clearHistory($type)
    {
        $this->getConnection()->delete($this->getMainTable(), ['type = ?' => (string)$type]);
    }

    /**
     * @param array $toDelete
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteByHistoryIds($toDelete)
    {
        if (!is_array($toDelete)) {
            return;
        }
        $this->getConnection()->delete($this->getMainTable(), ['history_id IN (?)' => array_map('intval', $toDelete)]);
    }
}
