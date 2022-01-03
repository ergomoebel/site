<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImport
 */


declare(strict_types=1);

namespace Amasty\ProductImport\Model\EntityLog\ResourceModel;

use Amasty\ProductImport\Model\EntityLog\EntityLog as EntityLogModel;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class EntityLog extends AbstractDb
{
    const TABLE_NAME = 'amasty_product_import_entity_log';

    public function __construct(
        Context $context,
        MetadataPool $metadataPool,
        $connectionName = null
    ) {
        if ($connectionName === null) {
            $metadata = $metadataPool->getMetadata(ProductInterface::class);
            $connectionName = $metadata->getEntityConnectionName();
        }

        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, EntityLogModel::ID);
    }

    /**
     * Load load entry by entity_id and process_identity fields
     *
     * @param AbstractModel $model
     * @param int $entityId
     * @param string $processIdentity
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadByEntityIdAndIdentity(AbstractModel $model, int $entityId, string $processIdentity)
    {
        $connection = $this->getConnection();

        $select = $connection->select()
            ->from($this->getMainTable())
            ->where(EntityLogModel::ENTITY_ID . ' = :entity_id')
            ->where(EntityLogModel::PROCESS_IDENTITY . ' = :process_identity');
        $bind = [
            ':entity_id' => $entityId,
            ':process_identity' => $processIdentity
        ];
        $data = $connection->fetchRow($select, $bind);
        if ($data) {
            $model->addData($data);
        }
        $this->_afterLoad($model);

        return $this;
    }

    /**
     * Delete entity log entries of given process identity
     *
     * @param string $processIdentity
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteEntriesByProcessIdentity(string $processIdentity)
    {
        $this->getConnection()
            ->delete(
                $this->getMainTable(),
                [EntityLogModel::PROCESS_IDENTITY . ' = ?' => $processIdentity]
            );

        return $this;
    }
}
