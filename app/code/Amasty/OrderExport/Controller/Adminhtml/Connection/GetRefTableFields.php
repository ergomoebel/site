<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */

declare(strict_types=1);

namespace Amasty\OrderExport\Controller\Adminhtml\Connection;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Controller\ResultFactory;

class GetRefTableFields extends Action
{
    const ADMIN_RESOURCE = 'Amasty_OrderExport::order_export_connections';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(
        Action\Context $context,
        ResourceConnection $resourceConnection
    ) {
        parent::__construct($context);
        $this->resourceConnection = $resourceConnection;
    }

    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        if ($table = $this->getRequest()->getParam('table')) {
            $tableData = $this->resourceConnection->getConnection()->describeTable($table);

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
