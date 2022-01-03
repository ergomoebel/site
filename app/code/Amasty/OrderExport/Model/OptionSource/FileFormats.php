<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */

declare(strict_types=1);

namespace Amasty\OrderExport\Model\OptionSource;

use Amasty\ExportCore\Export\Template\TemplateConfig;
use Magento\Framework\Data\OptionSourceInterface;

class FileFormats implements OptionSourceInterface
{
    /**
     * @var TemplateConfig
     */
    private $templateConfig;

    public function __construct(
        TemplateConfig $templateConfig
    ) {
        $this->templateConfig = $templateConfig;
    }

    public function toOptionArray()
    {
        $result = [];

        foreach ($this->templateConfig->all() as $code => $templateConfig) {
            $result[] = [
                'value' => $code,
                'label' => $templateConfig['name']
            ];
        }

        return $result;
    }
}
