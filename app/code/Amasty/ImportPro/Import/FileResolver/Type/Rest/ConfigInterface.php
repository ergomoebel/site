<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */

declare(strict_types=1);

namespace Amasty\ImportPro\Import\FileResolver\Type\Rest;

interface ConfigInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * @return string|null
     */
    public function getEndpoint(): ?string;

    /**
     * @param string|null $endpoint
     *
     * @return \Amasty\ImportPro\Import\FileResolver\Type\Rest\ConfigInterface
     */
    public function setEndpoint(?string $endpoint): ConfigInterface;

    /**
     * @return string|null
     */
    public function getAuthType(): ?string;

    /**
     * @param int|null $authType
     *
     * @return \Amasty\ImportPro\Import\FileResolver\Type\Rest\ConfigInterface
     */
    public function setAuthType(?string $authType): ConfigInterface;

    /**
     * @return int|null
     */
    public function getMethod(): ?int;

    /**
     * @param int|null $method
     *
     * @return \Amasty\ImportPro\Import\FileResolver\Type\Rest\ConfigInterface
     */
    public function setMethod(?int $method): ConfigInterface;

    /**
     * @return \Amasty\ImportPro\Import\FileResolver\Type\Rest\ConfigExtensionInterface
     */
    public function getExtensionAttributes()
        : \Amasty\ImportPro\Import\FileResolver\Type\Rest\ConfigExtensionInterface;

    /**
     * @param \Amasty\ImportPro\Import\FileResolver\Type\Rest\ConfigExtensionInterface $extensionAttributes
     *
     * @return \Amasty\ImportPro\Import\FileResolver\Type\Rest\ConfigInterface
     */
    public function setExtensionAttributes(
        \Amasty\ImportPro\Import\FileResolver\Type\Rest\ConfigExtensionInterface $extensionAttributes
    ): ConfigInterface;
}
