<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


namespace Amasty\OrderExport\Export\Config\RelationSource;

use Amasty\ExportCore\Export\Config\RelationSource\RelationSourceInterface;
use Amasty\ExportCore\Export\Config\RelationSource\Xml\RelationsConfigPrepare;
use Amasty\ExportCore\Export\SubEntity\Collector\OneToMany;
use Amasty\ExportCore\Export\SubEntity\Relation\RelationConfig;
use Amasty\OrderExport\Model\Connection\Connection;
use Amasty\OrderExport\Model\Connection\ResourceModel\CollectionFactory;

class ThirdPartyConnection implements RelationSourceInterface
{
    /**
     * @var RelationsConfigPrepare
     */
    private $relationsConfigPrepare;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        RelationsConfigPrepare $relationsConfigPrepare,
        CollectionFactory $collectionFactory
    ) {
        $this->relationsConfigPrepare = $relationsConfigPrepare;
        $this->collectionFactory = $collectionFactory;
    }

    public function get()
    {
        $result = [];
        $connections = $this->collectionFactory->create()->getItems();
        /** @var Connection $connection */
        foreach ($connections as $connection) {
            $entity = [];
            $entity[] = [
                'parent_entity'                       => $connection->getParentEntity(),
                RelationConfig::CHILD_ENTITY_CODE     => $connection->getEntityCode(),
                RelationConfig::SUB_ENTITY_FIELD_NAME => $connection->getTableToJoin(),
                RelationConfig::TYPE                  => 'one_to_many',
                'arguments'                           =>
                    [
                        OneToMany::PARENT_FIELD_NAME => [
                            'name'     => OneToMany::PARENT_FIELD_NAME,
                            'xsi:type' => 'string',
                            'value'    => $connection->getBaseTableKey()
                        ],
                        OneToMany::CHILD_FIELD_NAME  => [
                            'name'     => OneToMany::CHILD_FIELD_NAME,
                            'xsi:type' => 'string',
                            'value'    => $connection->getReferencedTableKey()
                        ]
                    ]
            ];
            $result[][$connection->getParentEntity()] = $this->relationsConfigPrepare->execute($entity);
        }

        return array_merge_recursive([], ...$result);
    }
}
