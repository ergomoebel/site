<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */


namespace Amasty\ImportPro\Model\Job\DataProvider;

use Amasty\ImportCore\Import\Config\EntityConfigProvider;
use Amasty\ImportPro\Model\Job\ResourceModel\Collection;
use Amasty\ImportPro\Model\Job\ResourceModel\CollectionFactory;
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
