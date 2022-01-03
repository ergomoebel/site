<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */

declare(strict_types=1);

namespace Amasty\ImportPro\Import\FileResolver\Type\Rest;

use Magento\Framework\DataObject;

class Config extends DataObject implements ConfigInterface
{
    const ENDPOINT = 'endpoint';
    const AUTH_TYPE = 'auth_type';
    const METHOD = 'method';

    /**
     * @var ConfigExtensionInterfaceFactory
     */
    private $extensionAttributesFactory;

    public function __construct(
        ConfigExtensionInterfaceFactory $extensionAttributesFactory,
        array $data = []
    ) {
        parent::__construct($data);
        $this->extensionAttributesFactory = $extensionAttributesFactory;
    }

    public function getEndpoint(): ?string
    {
        return $this->hasData(self::ENDPOINT) ? $this->getData(self::ENDPOINT) : null;
    }

    public function setEndpoint(?string $endpoint): ConfigInterface
    {
        return $this->setData(self::ENDPOINT, $endpoint);
    }

    public function getAuthType(): ?string
    {
        return $this->hasData(self::AUTH_TYPE) ? (string)$this->getData(self::AUTH_TYPE) : null;
    }

    public function setAuthType(?string $authType): ConfigInterface
    {
        return $this->setData(self::AUTH_TYPE, $authType);
    }

    public function getMethod(): ?int
    {
        return $this->hasData(self::METHOD) ? (int)$this->getData(self::METHOD) : null;
    }

    public function setMethod(?int $method): ConfigInterface
    {
        return $this->setData(self::METHOD, $method);
    }

    public function getExtensionAttributes()
        : \Amasty\ImportPro\Import\FileResolver\Type\Rest\ConfigExtensionInterface
    {
        if (null === $this->getData(self::EXTENSION_ATTRIBUTES_KEY)) {
            $this->setExtensionAttributes($this->extensionAttributesFactory->create());
        }

        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    public function setExtensionAttributes(
        \Amasty\ImportPro\Import\FileResolver\Type\Rest\ConfigExtensionInterface $extensionAttributes
    ): ConfigInterface {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
