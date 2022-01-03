<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerExport
 */

declare(strict_types=1);

namespace Amasty\CustomerExport\Model\OptionSource;

use Amasty\ExportCore\Api\Config\EntityConfigInterface;
use Amasty\ExportCore\Export\Config\RelationConfigProvider;
use Magento\Framework\Data\OptionSourceInterface;
use Amasty\ExportCore\Export\Config\EntityConfigProvider;

class ParentEntity implements OptionSourceInterface
{
    /**
     * @var EntityConfigProvider
     */
    private $entityConfigProvider;

    /**
     * @var RelationConfigProvider
     */
    private $relationConfigProvider;

    public function __construct(
        EntityConfigProvider $entityConfigProvider,
        RelationConfigProvider $relationConfigProvider
    ) {
        $this->entityConfigProvider = $entityConfigProvider;
        $this->relationConfigProvider = $relationConfigProvider;
    }

    public function toOptionArray()
    {
        $customerConfig = $this->entityConfigProvider->get('customer_entity');
        $options = [
            ['value' => '', 'label' => __('-- Please Select --')],
            ['value' => $customerConfig->getEntityCode(), 'label' => __($customerConfig->getName())]
        ];

        return array_merge(
            $options,
            $this->getNestedOptions($this->relationConfigProvider->get($customerConfig->getEntityCode()))
        );
    }

    public function getNestedOptions(?array $relationsConfig)
    {
        $result = [];
        if (!empty($relationsConfig)) {
            foreach ($relationsConfig as $relation) {
                $childEntity = $this->entityConfigProvider->get($relation->getChildEntityCode());
                $result[][] = ['value' => $childEntity->getEntityCode(), 'label' => __($childEntity->getName())];

                if ($relation->getRelations()) {
                    $result[] = $this->getNestedOptions($relation->getRelations());
                }
            }
        }

        return array_merge([], ...$result);
    }
}
