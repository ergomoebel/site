<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderImport
 */

declare(strict_types=1);

namespace Amasty\OrderImport\Import\Form;

use Amasty\ImportCore\Api\Config\EntityConfigInterface;
use Amasty\ImportCore\Api\Config\ProfileConfigInterface;
use Amasty\ImportCore\Api\FormInterface;
use Amasty\ImportCore\Import\Form\Fields\IdentifiersCollector;
use Amasty\ImportCore\Import\OptionSource\ValidationStrategy;
use Amasty\OrderImport\Model\OptionSource\CustomerMode;
use Amasty\OrderImport\Model\OptionSource\OrderIdentifier;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;

class ImportSettings extends \Amasty\ImportCore\Import\Form\General
{
    /**
     * @var CustomerMode
     */
    private $customerMode;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var IdentifiersCollector
     */
    private $identifiersCollector;

    public function __construct(
        ValidationStrategy $validationStrategy,
        UrlInterface $url,
        CustomerMode $customerMode,
        RequestInterface $request,
        IdentifiersCollector $identifiersCollector
    ) {
        parent::__construct($validationStrategy, $url);
        $this->customerMode = $customerMode;
        $this->request = $request;
        $this->identifiersCollector = $identifiersCollector;
    }

    public function getMeta(EntityConfigInterface $entityConfig, array $arguments = []): array
    {
        $meta = parent::getMeta($entityConfig, $arguments);

        $meta['import_behavior']['children']['customer_mode']['arguments']['data']['config'] = [
            'label' => __('Customer Mode'),
            'visible' => true,
            'dataScope' => 'customer_mode',
            'dataType' => 'select',
            'formElement' => 'select',
            'componentType' => 'select',
            'additionalClasses' => 'amimportcore-field',
            'options' => $this->customerMode->toOptionArray()
        ];

        if (!$this->request->getParam('id')) {
            $meta['import_behavior']['children']['autofill']['arguments']['data']['config'] = [
                'label' => __('Enable Autofill for Typical Use Cases'),
                'dataType' => 'boolean',
                'prefer' => 'toggle',
                'visible' => true,
                'dataScope' => 'autofill',
                'formElement' => 'checkbox',
                'componentType' => 'field',
                'additionalClasses' => 'amimportcore-field',
                'sortOrder' => 11,
                'valueMap' => ['true' => 1, 'false' => 0],
                'default' => 0,
                'tooltipTpl' => 'Amasty_ImportCore/form/element/tooltip',
                'tooltip' => [
                    'description' => __(
                        'If enabled, Fields Configuration will be automatically filled in'
                        . ' with the settings to perform the typical use cases for importing'
                        . ' orders from third-party systems.'
                    )
                ]
            ];
        }

        $meta['import_behavior']['children']['entity_identifier']['arguments']['data']['config'] = [
            'label' =>  'Order Identifier',
            'component' => 'Amasty_ImportPro/js/form/element/entity-identifier',
            'visible' => true,
            'dataScope' => 'entity_identifier',
            'dataType' => 'select',
            'formElement' => 'select',
            'componentType' => 'select',
            'additionalClasses' => 'amimportcore-field',
            'sortOrder' => 15,
            'options' => $this->identifiersCollector->collect($entityConfig)
        ];

        return $meta;
    }

    public function getData(ProfileConfigInterface $profileConfig): array
    {
        $importBehavior = parent::getData($profileConfig);

        $data = ['import_behavior' => $importBehavior];
        if ($customerMode = $profileConfig->getExtensionAttributes()->getCustomerMode()) {
            $data['import_behavior']['customer_mode'] = $customerMode;
        }

        if ($entityIdentifier = $profileConfig->getEntityIdentifier()) {
            $data['import_behavior']['entity_identifier'] = $entityIdentifier;
        }

        return $data;
    }

    public function prepareConfig(ProfileConfigInterface $profileConfig, RequestInterface $request): FormInterface
    {
        $params = $request->getParams();
        $importBehavior = $params['import_behavior'] ?? [];
        unset($params['import_behavior']);
        $params = array_merge_recursive($params, $importBehavior);
        $request->setParams($params);

        parent::prepareConfig($profileConfig, $request);

        if ($customerMode = $request->getParam('customer_mode')) {
            $profileConfig->getExtensionAttributes()->setCustomerMode($customerMode);
        }

        if ($entityIdentifier = $request->getParam('entity_identifier')) {
            $profileConfig->setEntityIdentifier($entityIdentifier);
        }

        return $this;
    }
}
