<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */

declare(strict_types=1);

namespace Amasty\ExportPro\Export\FileDestination\Type\Rest\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class ContentType implements OptionSourceInterface
{
    const JSON = 0;
    const XML = 1;

    public function toOptionArray(): array
    {
        return [
            ['value' => self::JSON, 'label' => __('JSON')],
            ['value' => self::XML, 'label' => __('XML')],
        ];
    }
}
