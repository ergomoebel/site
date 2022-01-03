<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


namespace Amasty\OrderExport\Export\Config\EntitySource;

use Amasty\ExportCore\Export\Config\EntityConfigFactory;
use Amasty\ExportCore\Export\Config\EntitySource\EntitySourceInterface;
use Amasty\ExportCore\Export\Config\EntitySource\Xml\FieldsConfigPrepare;
use Amasty\ExportCore\Export\FieldsClass\Describe;
use Amasty\ImportExportCore\Api\Config\ConfigClass\ConfigClassInterfaceFactory;
use Amasty\ImportExportCore\Config\Xml\ArgumentsPrepare;
use Amasty\ExportCore\Export\Config\CustomEntity\CollectionFactory as CustomCollectionFactory;
use Amasty\OrderExport\Model\Connection\Connection;
use Amasty\OrderExport\Model\Connection\ResourceModel\CollectionFactory;

class ThirdPartyConnection implements EntitySourceInterface
{
    /**
     * @var EntityConfigFactory
     */
    private $entityConfigFactory;

    /**
     * @var FieldsConfigPrepare
     */
    private $fieldsConfigPrepare;

    /**
     * @var ConfigClassInterfaceFactory
     */
    private $configClassFactory;

    /**
     * @var ArgumentsPrepare
     */
    private $argumentsPrepare;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        EntityConfigFactory $entityConfigFactory,
        ConfigClassInterfaceFactory $configClassFactory,
        ArgumentsPrepare $argumentsPrepare,
        CollectionFactory $collectionFactory,
        FieldsConfigPrepare $fieldsConfigPrepare
    ) {
        $this->entityConfigFactory = $entityConfigFactory;
        $this->fieldsConfigPrepare = $fieldsConfigPrepare;
        $this->configClassFactory = $configClassFactory;
        $this->argumentsPrepare = $argumentsPrepare;
        $this->collectionFactory = $collectionFactory;
    }

    public function get()
    {
        $result = [];
        $connections = $this->collectionFactory->create()->getItems();
        /** @var Connection $connection */
        foreach ($connections as $connection) {
            $entity = $this->entityConfigFactory->create();
            $entity->setEntityCode($connection->getEntityCode());
            $entity->setName($connection->getName());
            $entity->setDescription($connection->getName() ?? null);
            $entity->setHiddenInLists(1);
            $collectionFactory = $this->configClassFactory->create(
                [
                    'name'      => CustomCollectionFactory::class,
                    'arguments' => $this->argumentsPrepare->execute(
                        [
                            [
                                'name'     => CustomCollectionFactory::TABLE_NAME,
                                'xsi:type' => 'string',
                                'value'    => $connection->getTableToJoin(),
                            ],
                            [
                                'name'     => CustomCollectionFactory::ID_FILED,
                                'xsi:type' => 'string',
                                'value'    => $connection->getReferencedTableKey()
                            ]
                        ]
                    )
                ]
            );
            $entity->setCollectionFactory($collectionFactory);
            $entity->setFieldsConfig(
                $this->fieldsConfigPrepare->execute(
                    ['fieldsClass' => ['class' => Describe::class]],
                    $entity
                )
            );

            $result[] =  $entity;
        }

        return $result;
    }
}
