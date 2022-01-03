<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExportEntity
 */

declare(strict_types=1);

namespace Amasty\OrderExportEntity\Export\Filter\Type\CustomOption;

use Amasty\ExportCore\Api\Config\Entity\Field\FieldInterface;
use Amasty\ExportCore\Api\Config\Profile\FieldFilterInterface;
use Amasty\ExportCore\Api\Filter\FilterMetaInterface;

/**
 * Custom option filter meta
 */
class Meta implements FilterMetaInterface
{
    /**
     * @var ConfigInterfaceFactory
     */
    private $configFactory;

    /**
     * @var ValueProcessor
     */
    private $valueProcessor;

    public function __construct(
        ConfigInterfaceFactory $configFactory,
        ValueProcessor $valueProcessor
    ) {
        $this->configFactory = $configFactory;
        $this->valueProcessor = $valueProcessor;
    }

    public function getJsConfig(FieldInterface $field): array
    {
        return [
            'component' => 'Magento_Ui/js/form/element/textarea',
            'template' => 'Amasty_OrderExportEntity/form/field/custom-option-value',
            'notice' => __(
                'Specify pairs of custom option title and custom option value here. '
                . 'Firstly, specify the custom option title, then send the cursor to the next line '
                . '(using a Return key) and specify the custom option value.'
            ),
        ];
    }

    public function getConditions(FieldInterface $field): array
    {
        return [
            ['label' => __('is'), 'value' => 'eq'],
            ['label' => __('is not'), 'value' => 'neq'],
            ['label' => __('more or equal'), 'value' => 'gteq'],
            ['label' => __('less or equal'), 'value' => 'lteq'],
            ['label' => __('greater than'), 'value' => 'gt'],
            ['label' => __('less than'), 'value' => 'lt'],
            ['label' => __('like'), 'value' => 'like']
        ];
    }

    public function prepareConfig(FieldFilterInterface $filter, $value): FilterMetaInterface
    {
        $valueItems = $this->valueProcessor->getValueItems($value);
        if (count($valueItems)) {
            /** @var ConfigInterface $config */
            $config = $this->configFactory->create();
            $config->setValueItems($valueItems);
            $filter->getExtensionAttributes()->setCustomOptionFilter($config);
        }

        return $this;
    }

    public function getValue(FieldFilterInterface $filter)
    {
        /** @var ConfigInterface $config */
        $config = $filter->getExtensionAttributes()->getCustomOptionFilter();
        return $config
            ? $config->getValueItems()
            : null;
    }
}
