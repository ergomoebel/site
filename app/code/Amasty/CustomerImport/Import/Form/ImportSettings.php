<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerImport
 */

declare(strict_types=1);

namespace Amasty\CustomerImport\Import\Form;

use Amasty\ImportCore\Api\Config\EntityConfigInterface;
use Amasty\ImportCore\Api\Config\ProfileConfigInterface;
use Amasty\ImportCore\Api\FormInterface;
use Amasty\ImportCore\Import\OptionSource\ValidationStrategy;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;

class ImportSettings extends \Amasty\ImportCore\Import\Form\General
{
    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        ValidationStrategy $validationStrategy,
        UrlInterface $url,
        RequestInterface $request
    ) {
        parent::__construct($validationStrategy, $url);
        $this->request = $request;
    }

    public function getMeta(EntityConfigInterface $entityConfig, array $arguments = []): array
    {
        $meta = parent::getMeta($entityConfig, $arguments);

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
                        . ' customers from third-party systems.'
                    )
                ]
            ];
        }

        return $meta;
    }

    public function getData(ProfileConfigInterface $profileConfig): array
    {
        $importBehavior = parent::getData($profileConfig);

        return ['import_behavior' => $importBehavior];
    }

    public function prepareConfig(ProfileConfigInterface $profileConfig, RequestInterface $request): FormInterface
    {
        $params = $request->getParams();
        $importBehavior = $params['import_behavior'] ?? [];
        unset($params['import_behavior']);
        $params = array_merge_recursive($params, $importBehavior);
        $request->setParams($params);

        parent::prepareConfig($profileConfig, $request);

        return $this;
    }
}
