<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */

declare(strict_types=1);

namespace Amasty\OrderExport\Controller\Adminhtml\Connection;

use Amasty\ExportCore\Export\Action\Preparation\Collection\Factory;
use Amasty\ExportCore\Export\Config\EntityConfigProvider;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class GetMainTableFields extends Action
{
    const ADMIN_RESOURCE = 'Amasty_OrderExport::order_export_connections';

    /**
     * @var Factory
     */
    private $collectionFactory;

    /**
     * @var EntityConfigProvider
     */
    private $entityConfigProvider;

    public function __construct(
        Action\Context $context,
        Factory $collectionFactory,
        EntityConfigProvider $entityConfigProvider
    ) {
        parent::__construct($context);
        $this->collectionFactory = $collectionFactory;
        $this->entityConfigProvider = $entityConfigProvider;
    }

    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        if ($table = $this->getRequest()->getParam('table')) {
            $entityConfig = $this->entityConfigProvider->get($table);
            $collection = $this->collectionFactory->create($entityConfig);
            $collection->getResource()->getConnection()->describeTable($collection->getMainTable());
            $tableData = $collection->getResource()->getConnection()->describeTable($collection->getMainTable());

            $result->setData($this->formatToOptions($tableData));
        }

        return $result;
    }

    protected function formatToOptions(array $tableData): array
    {
        $result = [];

        foreach ($tableData as $field) {
            $result[] = ['value' => $field['COLUMN_NAME'], 'label' => $field['COLUMN_NAME'], 'path' => ''];
        }

        return $result;
    }
}
