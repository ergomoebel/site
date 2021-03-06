<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerImport
 */


declare(strict_types=1);

namespace Amasty\CustomerImport\Ui\DataProvider\Profile;

use Amasty\CustomerImport\Model\Profile\ResourceModel\CollectionFactory;
use Magento\Framework\UrlInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class Listing extends AbstractDataProvider
{
    /**
     * @var UrlInterface
     */
    private $url;

    public function __construct(
        CollectionFactory $collectionFactory,
        UrlInterface $url,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->url = $url;
    }
}
