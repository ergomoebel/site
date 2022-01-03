<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */

declare(strict_types=1);

namespace Amasty\ExportPro\Export\FileDestination\Type\Rest;

use Magento\Framework\DataObject;

class Config extends DataObject implements ConfigInterface
{
    const ENDPOINT = 'endpoint';
    const AUTH_TYPE = 'auth_type';
    const METHOD = 'method';
    const CONTENT_TYPE = 'content_type';
    const IS_SEPARATE_REQUEST = 'is_separate_request';
    const HEADER = 'header';
    const FOOTER = 'footer';
    const ITEM = 'item';
    const ITEM_PATH = 'item_path';

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

    public function getContentType(): ?int
    {
        return $this->hasData(self::CONTENT_TYPE) ? (int)$this->getData(self::CONTENT_TYPE) : null;
    }

    public function setContentType(?int $contentType): ConfigInterface
    {
        return $this->setData(self::CONTENT_TYPE, $contentType);
    }

    public function getExtensionAttributes()
        : \Amasty\ExportPro\Export\FileDestination\Type\Rest\ConfigExtensionInterface
    {
        if (null === $this->getData(self::EXTENSION_ATTRIBUTES_KEY)) {
            $this->setExtensionAttributes($this->extensionAttributesFactory->create());
        }

        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    public function setExtensionAttributes(
        \Amasty\ExportPro\Export\FileDestination\Type\Rest\ConfigExtensionInterface $extensionAttributes
    ): ConfigInterface {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
