<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Export\Template\Type\Xlsx;

use Amasty\ExportCore\Api\ExportProcessInterface;
use Amasty\ExportCore\Api\Template\RendererInterface;
use Amasty\ExportPro\Export\Template\Type\Spout\Renderer as SpoutRenderer;

class Renderer extends SpoutRenderer implements RendererInterface
{
    const TYPE_ID = 'xlsx';
    const EXTENSION = 'xlsx';
    const WRITER_TYPE = \Box\Spout\Common\Type::XLSX;

    public function getConfig(ExportProcessInterface $exportProcess)
    {
        return $exportProcess->getProfileConfig()->getExtensionAttributes()->getXlsxTemplate();
    }
}
