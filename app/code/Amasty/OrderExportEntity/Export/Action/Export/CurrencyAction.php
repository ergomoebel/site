<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExportEntity
 */

declare(strict_types=1);

namespace Amasty\OrderExportEntity\Export\Action\Export;

use Amasty\ExportCore\Api\ActionInterface;
use Amasty\ExportCore\Api\Config\Profile\FieldsConfigInterface;
use Amasty\ExportCore\Api\ExportProcessInterface;
use Amasty\OrderExportEntity\Export\DataHandling\FieldModifier\OrderPrice;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;
use Magento\Sales\Api\Data\OrderInterface;

class CurrencyAction implements ActionInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    public function execute(ExportProcessInterface $exportProcess)
    {
        $data = $exportProcess->getData();
        $orderIds = array_column($data, OrderInterface::ENTITY_ID);
        $orderIdCurrencyMapping = $this->getCurrencyData($orderIds);

        foreach ($data as &$row) {
            $orderCurrency = $orderIdCurrencyMapping[$row[OrderInterface::ENTITY_ID]];
            $row[OrderPrice::CURRENCY_IDENTIFIER] = $orderCurrency;
        }

        if ($fieldsConfig = $exportProcess->getProfileConfig()->getFieldsConfig()) {
            $this->addCurrencyData($fieldsConfig, $data);
        }
        $exportProcess->setData($data);
    }

    private function addCurrencyData(FieldsConfigInterface $fieldsConfig, array &$data, string $orderCurrency = 'USD')
    {
        foreach ($data as &$row) {
            if (!empty($fieldsConfig->getSubEntitiesFieldsConfig())) {
                foreach ($fieldsConfig->getSubEntitiesFieldsConfig() as $subEntityConfig) {
                    if (empty($row[$subEntityConfig->getName()])) {
                        continue;
                    }
                    $this->addCurrencyData(
                        $subEntityConfig,
                        $row[$subEntityConfig->getName()],
                        $row[OrderPrice::CURRENCY_IDENTIFIER] ?? $orderCurrency
                    );
                }
            }
        }

        if (!empty($fieldsConfig->getFields())) {
            $row[OrderPrice::CURRENCY_IDENTIFIER] = $row[OrderPrice::CURRENCY_IDENTIFIER] ?? $orderCurrency;
        }
    }

    private function getCurrencyData(array $orderIds): array
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select();
        $select->from($this->resourceConnection->getTableName('sales_order'))
            ->reset(Select::COLUMNS)
            ->columns([OrderInterface::ENTITY_ID, OrderPrice::CURRENCY_IDENTIFIER])
            ->where(OrderInterface::ENTITY_ID . ' IN (?)', $orderIds);
        $result = [];
        foreach ($connection->fetchAll($select) as $row) {
            $result[$row[OrderInterface::ENTITY_ID]] = $row[OrderPrice::CURRENCY_IDENTIFIER];
        }

        return $result;
    }

    // phpcs:ignore
    public function initialize(ExportProcessInterface $exportProcess)
    {
    }
}
