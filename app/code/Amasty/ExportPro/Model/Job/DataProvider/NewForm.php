<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


namespace Amasty\ExportPro\Model\Job\DataProvider;

use Amasty\ExportCore\Export\Config\EntityConfigProvider;
use Amasty\ExportPro\Model\Job\ResourceModel\Collection;
use Amasty\ExportPro\Model\Job\ResourceModel\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class NewForm extends AbstractDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;
    /**
     * @var EntityConfigProvider
     */
    private $entityConfigProvider;
    /**
     * @var UrlInterface
     */
    private $url;
    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        CollectionFactory $collectionFactory,
        EntityConfigProvider $entityConfigProvider,
        UrlInterface $url,
        RequestInterface $request,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->entityConfigProvider = $entityConfigProvider;
        $this->url = $url;
        $this->request = $request;
    }
}
