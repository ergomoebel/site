<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


declare(strict_types=1);

namespace Amasty\OrderExport\Ui\DataProvider\Connection;

use Amasty\OrderExport\Model\Connection\Connection;
use Amasty\OrderExport\Model\Connection\Repository;
use Amasty\OrderExport\Model\Connection\ResourceModel\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class Form extends AbstractDataProvider
{
    const CONNECTION_DATA = 'connectionData';

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var Repository
     */
    private $repository;

    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        Repository $repository,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->repository = $repository;
    }

    public function getData(): array
    {
        $data = parent::getData();
        if ($data['totalRecords'] > 0) {
            $connectionId = (int)$data['items'][0][Connection::ID];
            $connectionModel = $this->repository->getById($connectionId);
            $data[$connectionId] = $connectionModel->getData();
        }
        if ($savedData = $this->dataPersistor->get(self::CONNECTION_DATA)) {
            $savedConnectionId = isset($savedData[Connection::ID]) ? $savedData[Connection::ID] : null;
            if (isset($data[$savedConnectionId])) {
                $data[$savedConnectionId] = array_merge($data[$savedConnectionId], $savedData);
            } else {
                $data[$savedConnectionId] = $savedData;
            }
            $this->dataPersistor->clear(self::CONNECTION_DATA);
        }

        return $data;
    }
}
