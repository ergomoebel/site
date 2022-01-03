<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExport
 */


namespace Amasty\ProductExport\Model;

use Amasty\ProductExport\Model\Profile\Profile as ProfileModel;
use Amasty\ProductExport\Model\Profile\ResourceModel\CollectionFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\Module\Manager;

class ProductExportList extends \Magento\Framework\DataObject implements \Iterator
{
    const MODULE_PGRID = 'Amasty_Pgrid';

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * current cursor position
     * @var int
     */
    private $position = 0;

    public function __construct(
        CollectionFactory $collectionFactory,
        UrlInterface $url,
        Manager $moduleManager,
        array $data = []
    ) {
        $this->moduleManager = $moduleManager;
        $this->position = 0;
        $options = isset($data['defaultOptions']) && $this->isPgridlEnable()
            ? array_values($data['defaultOptions'])
            : [];
        $collection = $collectionFactory->create();

        /** @var ProfileModel $item */
        foreach ($collection->getItems() as $item) {
            $options[] = [
                'value' => $item->getId(),
                'label' => $item->getName(),
                'url' => 'amproductexport/product/export/profile_id/' . $item->getId(),
                'is_amasty_profile' => true,
                'statusUrl' => $url->getUrl('amexport/export/status'),
                'startUrl' => $url->getUrl('amproductexport/profile/export'),
                'downloadUrl' => $url->getUrl(
                    'amexport/export/download',
                    ['processIdentity' => '_process_identity_']
                )
            ];
        }

        parent::__construct($options);
    }

    /**
     * @return bool
     */
    private function isPgridlEnable()
    {
        return $this->moduleManager->isEnabled(self::MODULE_PGRID);
    }

    /**
     * reset array position
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * current item
     * @return mixed
     */
    public function current()
    {
        return $this->getDataByKey($this->position);
    }

    /**
     * current key
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * set cursor to next element
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return $this->getDataByKey($this->position) !== null;
    }
}
