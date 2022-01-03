<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


namespace Amasty\ExportPro\Model\LastExportedId;

use Amasty\ExportPro\Api\Data\LastExportedIdInterface;
use Amasty\ExportPro\Api\Data\LastExportedIdInterfaceFactory;
use Amasty\ExportPro\Api\LastExportedIdRepositoryInterface;
use Amasty\ExportPro\Model\LastExportedId\ResourceModel\CollectionFactory;
use Amasty\ExportPro\Model\LastExportedId\ResourceModel\LastExportedId as LastExportedIdResource;
use Magento\Framework\Exception\CouldNotSaveException;

class Repository implements LastExportedIdRepositoryInterface
{
    /**
     * @var LastExportedIdInterfaceFactory
     */
    private $lastExportedIdFactory;

    /**
     * @var LastExportedIdResource
     */
    private $lastExportedIdResource;

    /**
     * @var CollectionFactory
     */
    private $lastExportedIdCollectionFactory;

    public function __construct(
        LastExportedIdInterfaceFactory $lastExportedIdFactory,
        LastExportedIdResource $lastExportedIdResource,
        CollectionFactory $lastExportedIdCollectionFactory
    ) {
        $this->lastExportedIdFactory = $lastExportedIdFactory;
        $this->lastExportedIdResource = $lastExportedIdResource;
        $this->lastExportedIdCollectionFactory = $lastExportedIdCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function save(LastExportedIdInterface $lastExportedId)
    {
        try {
            $this->lastExportedIdResource->save($lastExportedId);
        } catch (\Exception $e) {
            if ($lastExportedId->getId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save last run data ID %1. Error: %2',
                        [$lastExportedId->getId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save last run data. Error: %1', $e->getMessage()));
        }
    }

    public function getByTypeAndExternalId(string $type, int $externalId)
    {
        return $this->lastExportedIdCollectionFactory->create()
            ->addFieldToFilter(LastExportedId::EXTERNAL_ID, $externalId)
            ->addFieldToFilter(LastExportedId::TYPE, $type)
            ->getFirstItem();
    }
}
