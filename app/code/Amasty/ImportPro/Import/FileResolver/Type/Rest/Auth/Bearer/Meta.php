<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */

declare(strict_types=1);

namespace Amasty\ImportPro\Import\FileResolver\Type\Rest\Auth\Bearer;

use Amasty\ImportCore\Api\Config\EntityConfigInterface;
use Amasty\ImportCore\Api\Config\ProfileConfigInterface;
use Amasty\ImportCore\Api\FormInterface;
use Magento\Framework\App\RequestInterface;

class Meta implements \Amasty\ImportCore\Api\FormInterface
{
    const CODE = 'bearer';
    const DATASCOPE = 'auth-bearer.';

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
            'bearer_token' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Token'),
                            'validation' => [
                                'required-entry' => true
                            ],
                            'dataType' => 'text',
                            'formElement' => 'input',
                            'elementTmpl' => 'Amasty_ImportPro/form/element/password',
                            'visible' => true,
                            'componentType' => 'input',
                            'dataScope' => self::DATASCOPE . 'bearer_token'
                        ]
                    ]
                ]
            ],
        ];
    }

    public function getData(ProfileConfigInterface $profileConfig): array
    {
        /** @var ConfigInterface $config */
        $config = $profileConfig->getExtensionAttributes()->getRestFileResolver()
            ->getExtensionAttributes()->getBearer();
        if ($config) {
            return [
                'auth-bearer' => [
                    'bearer_token' => $config->getToken()
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
        $requestConfig = $request->getParam('auth-bearer') ?? [];
        if (isset($requestConfig['bearer_token'])) {
            $config->setToken($requestConfig['bearer_token']);
        }

        $profileConfig->getExtensionAttributes()->getRestFileResolver()
            ->getExtensionAttributes()->setBearer($config);

        return $this;
    }
}
