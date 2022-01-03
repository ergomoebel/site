<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImportEntity
 */


declare(strict_types=1);

namespace Amasty\ProductImportEntity\Import\Behavior\Product\Save\Link;

use Amasty\ImportCore\Api\Behavior\BehaviorResultInterface;
use Amasty\ProductImportEntity\Import\Behavior\Product\AbstractDirectBehavior;
use Magento\Framework\DB\Adapter\AdapterInterface;

abstract class AbstractLink extends AbstractDirectBehavior
{
    /**
     * @var array
     */
    private $linkAttributes;

    /**
     * @inheritDoc
     */
    public function execute(array &$data, ?string $customIdentifier = null): BehaviorResultInterface
    {
        $result = $this->saveLinks($data);
        $this->saveAttributeValues($data);

        return $result;
    }

    /**
     * Get link type Id
     *
     * @return int
     */
    abstract protected function getLinkTypeId();

    /**
     * Save product links rows
     *
     * @param array $data
     * @return BehaviorResultInterface
     * @throws \Exception
     */
    private function saveLinks(array &$data): BehaviorResultInterface
    {
        $result = $this->resultFactory->create();
        if (empty($data)) {
            return $result;
        }

        $links = $this->getLinks($data);

        $newIds = [];
        $updatedIds = [];
        $connection = $this->getConnection();
        foreach ($data as &$item) {
            $identityKey = isset($item['product_id']) && isset($item['linked_product_id'])
                ? $item['product_id'] . '-' . $item['linked_product_id']
                : null;

            if ($identityKey && isset($links[$identityKey])) {
                $existentLinkId = $links[$identityKey]['link_id'];

                $updatedIds[] = $existentLinkId;
                $item['link_id'] = $existentLinkId;

                continue;
            }

            if ($identityKey) {
                $tableName = $this->getTableName('catalog_product_link');

                $bind = [
                    'product_id' => $item['product_id'],
                    'linked_product_id' => $item['linked_product_id'],
                    'link_type_id' => $this->getLinkTypeId(),
                ];
                if (isset($item['link_id'])) {
                    $bind['link_id'] = $item['link_id'];
                }
                $connection->insertOnDuplicate($tableName, [$bind]);
                $newLinkId = $connection->lastInsertId($tableName);

                $newIds[] = $newLinkId;
                $item['link_id'] = $newLinkId;
            }
        }

        $result->setUpdatedIds($updatedIds);
        $result->setNewIds($newIds);

        return $result;
    }

    /**
     * Get stored links
     *
     * @param array $data
     * @return array
     */
    private function getLinks(array $data): array
    {
        $connection = $this->getConnection();

        $conditions = [];
        foreach ($data as $item) {
            if (isset($item['product_id']) && isset($item['linked_product_id'])) {
                $conditions[] = implode(
                    ' AND ',
                    [
                        $connection->quoteInto('product_id = ?', $item['product_id']),
                        $connection->quoteInto('linked_product_id = ?', $item['linked_product_id'])
                    ]
                );
            }
        }
        $select = $connection->select()->from(
            $this->getTableName('catalog_product_link'),
            [
                'product_id',
                'linked_product_id',
                'link_id'
            ]
        )->where(
            '(' . implode(' OR ', $conditions) . ')'
        )->where(
            'link_type_id = ?',
            $this->getLinkTypeId()
        );

        $links = [];
        $linkRows = $connection->fetchAll($select);
        foreach ($linkRows as $linkData) {
            $links[$linkData['product_id'] . '-' . $linkData['linked_product_id']] = $linkData;
        }

        return $links;
    }

    /**
     * Save link attribute values
     *
     * @param array $data
     * @return void
     * @throws \Exception
     */
    private function saveAttributeValues(array $data)
    {
        $linkAttributes = $this->getLinkAttributes();
        $linkIds = array_column($data, 'link_id');
        foreach ($linkAttributes as $attrCode => $attrInfo) {
            $attributeId = $attrInfo['id'];
            $values = $this->getAttributeValues(
                $linkIds,
                $attrInfo['type'],
                $attributeId
            );

            $toInsert = [];
            $toUpdate = [];

            foreach ($data as $item) {
                if (!isset($item['link_id']) || !isset($item[$attrCode])) {
                    continue;
                }

                $linkId = $item['link_id'];
                if (isset($values[$linkId])) {
                    $value = $values[$linkId]['value'];
                    if ($item[$attrCode] != $value) {
                        $toUpdate[] = [
                            'product_link_attribute_id' => $attributeId,
                            'link_id' => $linkId,
                            'value' => $item[$attrCode]
                        ];
                    }
                } else {
                    $toInsert[] = [
                        'product_link_attribute_id' => $attributeId,
                        'link_id' => $linkId,
                        'value' => $item[$attrCode]
                    ];
                }
            }

            $connection = $this->getConnection();

            if ($toInsert) {
                $insertColumns = [
                    'product_link_attribute_id',
                    'link_id',
                    'value',
                ];
                $connection->insertArray(
                    $this->getAttributeValueTable($attrInfo['type']),
                    $insertColumns,
                    $toInsert,
                    AdapterInterface::INSERT_IGNORE
                );
            }

            if ($toUpdate) {
                $connection->insertOnDuplicate(
                    $this->getAttributeValueTable($attrInfo['type']),
                    $toUpdate,
                    ['value']
                );
            }
        }
    }

    /**
     * Get stored attribute values
     *
     * @param array $linkIds
     * @param string $attributeType
     * @param $attributeId
     * @return array
     * @throws \Exception
     */
    private function getAttributeValues(array $linkIds, string $attributeType, $attributeId): array
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getAttributeValueTable($attributeType),
            [
                'value_id',
                'link_id',
                'value'
            ]
        )->where(
            'product_link_attribute_id = ?',
            $attributeId
        )->where(
            'link_id IN (?)',
            $linkIds
        );

        $values = [];
        $valueRows = $connection->fetchAll($select);
        foreach ($valueRows as $valueData) {
            $values[$valueData['link_id']] = $valueData;
        }

        return $values;
    }

    /**
     * Get attribute value table name
     *
     * @param string $attributeType
     * @return string
     * @throws \Exception
     */
    private function getAttributeValueTable(string $attributeType): string
    {
        return $this->getTableName('catalog_product_link_attribute_' . $attributeType);
    }

    /**
     * Get link attributes
     *
     * @return array
     * @throws \Exception
     */
    protected function getLinkAttributes()
    {
        if (!$this->linkAttributes) {
            $this->linkAttributes = [];

            $connection = $this->getConnection();
            $select = $connection->select()->from(
                $this->getTableName('catalog_product_link_attribute'),
                [
                    'id' => 'product_link_attribute_id',
                    'code' => 'product_link_attribute_code',
                    'type' => 'data_type'
                ]
            )->where(
                'link_type_id = ?',
                $this->getLinkTypeId()
            );

            $attributes = $connection->fetchAll($select);
            foreach ($attributes as $attributeData) {
                $this->linkAttributes[$attributeData['code']] = $attributeData;
            }
        }

        return $this->linkAttributes;
    }
}
