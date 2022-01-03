<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */


declare(strict_types=1);

namespace Amasty\ImportPro\Import\Source\Type\Ods;

class Generator extends \Amasty\ImportPro\Import\Source\Type\Spout\Generator
{
    public function getExtension(): string
    {
        return Reader::TYPE_ID;
    }
}
