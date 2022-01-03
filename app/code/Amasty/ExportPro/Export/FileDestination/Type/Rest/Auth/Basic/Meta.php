<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */

declare(strict_types=1);

namespace Amasty\ExportPro\Export\FileDestination\Type\Rest\Auth\Basic;

use Amasty\ExportCore\Api\Config\EntityConfigInterface;
use Amasty\ExportCore\Api\Config\ProfileConfigInterface;
use Amasty\ExportCore\Api\FormInterface;
use Magento\Framework\App\RequestInterface;

class Meta implements \Amasty\ExportCore\Api\FormInterface
{
    const CODE = 'basic';
    const DATASCOPE = 'auth-basic.';

    /**
     * @var ConfigInterfaceFactory
     */
    private $configFactory;

    public function __construct(ConfigInterfaceFactory $configFactory)
    {
        $this->configFactory = $configFactory;
    }

    public function getMeta(EntityConfigInterface $entityConfig, array $arguments = []): array
    {
        return [
            'basic_username' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Username'),
                            'validation' => [
                                'required-entry' => true
                            ],
                            'dataType' => 'text',
                            'formElement' => 'input',
                            'visible' => true,
                            'componentType' => 'input',
                            'dataScope' => self::DATASCOPE . 'username'
                        ]
                    ]
                ]
            ],
            'rest_file.basic_password' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Password'),
                            'validation' => [
                                'required-entry' => true
                            ],
                            'dataType' => 'text',
                            'formElement' => 'input',
                            'elementTmpl' => 'Amasty_ExportPro/form/element/password',
                            'visible' => true,
                            'componentType' => 'input',
                            'dataScope' => self::DATASCOPE . 'password'
                        ]
                    ]
                ]
            ]
        ];
    }

    public function getData(ProfileConfigInterface $profileConfig): array
    {
        /** @var ConfigInterface $config */
        $config = $profileConfig->getExtensionAttributes()->getRestFileDestination()
            ->getExtensionAttributes()->getBasic();
        if ($config) {
            return [
                'auth-basic' => [
                    'username' => $config->getUsername(),
                    'password' => $config->getPassword()
                ]
            ];
        }

        return [];
    }

    public function prepareConfig(ProfileConfigInterface $profileConfig, RequestInterface $request): FormInterface
    {
        /** @var ConfigInterface $config */
        $config = $this->configFactory->create();
        /** @var ProfileConfigInterface $profileConfig */
        $requestConfig = $request->getParam('auth-basic') ?? [];
        if (isset($requestConfig['username'])) {
            $config->setUsername($requestConfig['username']);
        }
        if (isset($requestConfig['password'])) {
            $config->setPassword($requestConfig['password']);
        }

        $profileConfig->getExtensionAttributes()->getRestFileDestination()
            ->getExtensionAttributes()->setBasic($config);

        return $this;
    }
}
