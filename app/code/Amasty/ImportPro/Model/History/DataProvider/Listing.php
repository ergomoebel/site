<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */


namespace Amasty\ImportPro\Model\History\DataProvider;

use Amasty\ImportCore\Api\ImportResultInterface;
use Amasty\ImportCore\Api\ImportResultInterfaceFactory;
use Amasty\ImportPro\Model\History\History;
use Amasty\ImportPro\Model\History\ResourceModel\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;

class Listing extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var ImportResultInterfaceFactory
     */
    private $importResultFactory;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Json
     */
    private $serializer;

    public function __construct(
        CollectionFactory $collectionFactory,
        ImportResultInterfaceFactory $importResultFactory,
        RequestInterface $request,
        UrlInterface $url,
        Json $serializer,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        if ($request->getParam('job_type')) {
            $this->collection->addFieldToFilter(History::TYPE, $request->getParam('job_type'));
        }
        if ($request->getParam('job_id') != 'all') {
            $this->collection->addFieldToFilter(History::JOB_ID, $request->getParam('job_id', 0));
        }
        $this->importResultFactory = $importResultFactory;
        $this->url = $url;
        $this->request = $request;
        $this->serializer = $serializer;
    }

    public function getData()
    {
        $data = parent::getData();

        if (!empty($data['items'])) {
            foreach ($data['items'] as &$item) {
                if (!empty($item[History::LOG])) {
                    /** @var ImportResultInterface $importResult */
                    $importResult = $this->importResultFactory->create();
                    $importResult->unserialize($item[History::LOG]);
                    $item['records'] = __(
                        'Created: %1, Updated: %2, Deleted: %3',
                        [
                            $importResult->getRecordsAdded(),
                            $importResult->getRecordsUpdated(),
                            $importResult->getRecordsDeleted()
                        ]
                    );
                    //phpcs:ignore Magento2.Performance.ForeachArrayMerge.ForeachArrayMerge
                    $logMessages = array_merge(
                        $importResult->getMessages(),
                        $importResult->getPreparedValidationMessages()
                    );
                    $item['messages'] = $logMessages
                        ? $this->serializer->serialize($logMessages)
                        : '';
                }
            }
        }

        return $data;
    }
}
