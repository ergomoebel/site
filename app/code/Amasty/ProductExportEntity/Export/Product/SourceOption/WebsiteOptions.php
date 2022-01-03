<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExportEntity
 */

declare(strict_types=1);

namespace Amasty\ProductExportEntity\Export\Product\SourceOption;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Store\Model\StoreManagerInterface;

class WebsiteOptions implements OptionSourceInterface
{
    const DEFAULT_WEBSITE_LABEL = 'Admin';

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var bool
     */
    private $withDefault;

    /**
     * Default website label
     *
     * @var string
     */
    private $defaultWebsiteLabel;

    /**
     * @var array
     */
    private $options;

    public function __construct(
        StoreManagerInterface $storeManager,
        $withDefault = false,
        $defaultWebsiteLabel = self::DEFAULT_WEBSITE_LABEL
    ) {
        $this->storeManager = $storeManager;
        $this->withDefault = $withDefault;
        $this->defaultWebsiteLabel = $defaultWebsiteLabel;
    }

    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = [];

            if ($this->withDefault) {
                $this->options[] = [
                    'value' => 0,
                    'label' => __($this->defaultWebsiteLabel)
                ];
            }
            foreach ($this->storeManager->getWebsites() as $website) {
                $this->options[] = [
                    'value' => $website->getId(),
                    'label' => $website->getName()
                ];
            }
        }

        return $this->options;
    }
}
