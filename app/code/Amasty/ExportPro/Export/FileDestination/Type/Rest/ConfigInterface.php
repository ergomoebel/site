<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */

declare(strict_types=1);

namespace Amasty\ExportPro\Export\FileDestination\Type\Rest;

interface ConfigInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * @return string|null
     */
    public function getEndpoint(): ?string;

    /**
     * @param string|null $endpoint
     *
     * @return \Amasty\ExportPro\Export\FileDestination\Type\Rest\ConfigInterface
     */
    public function setEndpoint(?string $endpoint): ConfigInterface;

    /**
     * @return string|null
     */
    public function getAuthType(): ?string;

    /**
     * @param int|null $authType
     *
     * @return \Amasty\ExportPro\Export\FileDestination\Type\Rest\ConfigInterface
     */
    public function setAuthType(?string $authType): ConfigInterface;

    /**
     * @return int|null
     */
    public function getMethod(): ?int;

    /**
     * @param int|null $method
     *
     * @return \Amasty\ExportPro\Export\FileDestination\Type\Rest\ConfigInterface
     */
    public function setMethod(?int $method): ConfigInterface;

    /**
     * @return int|null
     */
    public function getContentType(): ?int;

    /**
     * @param int|null $contentType
     *
     * @return \Amasty\ExportPro\Export\FileDestination\Type\Rest\ConfigInterface
     */
    public function setContentType(?int $contentType): ConfigInterface;

    /**
     * @return \Amasty\ExportPro\Export\FileDestination\Type\Rest\ConfigExtensionInterface
     */
    public function getExtensionAttributes()
        : \Amasty\ExportPro\Export\FileDestination\Type\Rest\ConfigExtensionInterface;

    /**
     * @param \Amasty\ExportPro\Export\FileDestination\Type\Rest\ConfigExtensionInterface $extensionAttributes
     *
     * @return \Amasty\ExportPro\Export\FileDestination\Type\Rest\ConfigInterface
     */
    public function setExtensionAttributes(
        \Amasty\ExportPro\Export\FileDestination\Type\Rest\ConfigExtensionInterface $extensionAttributes
    ): ConfigInterface;
}
