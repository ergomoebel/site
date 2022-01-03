<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */


namespace Amasty\ImportPro\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;
use Amasty\ImportCore\Import\Config\EntityConfigProvider;

class EntityType implements OptionSourceInterface
{
    /**
     * @var EntityConfigProvider
     */
    private $entityConfigProvider;

    public function __construct(
        EntityConfigProvider $entityConfigProvider
    ) {
        $this->entityConfigProvider = $entityConfigProvider;
    }

    public function toOptionArray()
    {
        $options = [];

        foreach ($this->entityConfigProvider->getConfig() as $entity) {
            if (!$entity->isHiddenInLists()) {
                $options[] = ['label' => $entity->getName(), 'value' => $entity->getEntityCode()];
            }
        }

        return $options;
    }
}
