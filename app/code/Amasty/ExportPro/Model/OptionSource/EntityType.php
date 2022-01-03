<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


namespace Amasty\ExportPro\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;
use Amasty\ExportCore\Export\Config\EntityConfigProvider;

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

        foreach ($this->entityConfigProvider->getConfig() as $entityCode => $entity) {
            $options[] = ['label' => $entity['name'], 'value' => $entityCode];
        }

        return $options;
    }
}
