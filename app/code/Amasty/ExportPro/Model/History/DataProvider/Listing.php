<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


namespace Amasty\ExportPro\Model\History\DataProvider;

use Amasty\ExportCore\Api\ExportResultInterfaceFactory;
use Amasty\ExportPro\Model\History\History;
use Amasty\ExportPro\Model\History\ResourceModel\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;

class Listing extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var ExportResultInterfaceFactory
     */
    private $exportResultFactory;

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
        ExportResultInterfaceFactory $exportResultFactory,
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
        $this->exportResultFactory = $exportResultFactory;
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
                    $exportResult = $this->exportResultFactory->create();
                    $exportResult->unserialize($item[History::LOG]);
                    $item['records'] = $exportResult->getRecordsProcessed() . '/' . $exportResult->getTotalRecords();
                    $item['messages'] = $exportResult->getMessages()
                        ? $this->serializer->serialize($exportResult->getMessages())
                        : '';

                    if (!$exportResult->isFailed() && empty($item[History::IS_DELETED_FILE])) {
                        $item['download_link'] = $this->url->getUrl(
                            'amexport/export/download/',
                            ['processIdentity' => $item[History::IDENTITY]]
                        );
                    }
                }
            }
        }

        return $data;
    }

    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        switch ($filter->getField()) {
            case 'records':
                $filter->setField(History::LOG);
                $filter->setValue('%"recordsProcessed":' . trim($filter->getValue(), '%') . '%');
                break;
            case 'messages':
                $this->collection->addFieldToFilter(History::LOG, ['nlike' => '%"messages":[]%']);
                $filter->setField(History::LOG);
                break;
        }
        parent::addFilter($filter);
    }

    public function addOrder($field, $direction)
    {
        if ($field == 'messages' || $field == 'records') {
            $field = History::LOG;
        }
        parent::addOrder($field, $direction);
    }
}
