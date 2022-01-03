<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Export\PostProcessing\Type\Compress;

use Amasty\ExportCore\Api\Config\EntityConfigInterface;
use Amasty\ExportCore\Api\Config\ProfileConfigInterface;
use Amasty\ExportCore\Api\FormInterface;
use Magento\Framework\App\RequestInterface;

class Meta implements FormInterface
{
    const TYPE_ID = 'compress';

    public function getMeta(EntityConfigInterface $entityConfig, array $arguments = []): array
    {
        return []; // TODO add some settings
    }

    public function prepareConfig(ProfileConfigInterface $profileConfig, RequestInterface $request): FormInterface
    {
        return $this;
    }

    public function getData(ProfileConfigInterface $profileConfig): array
    {
        return [];
    }
}
