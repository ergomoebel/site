<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


namespace Amasty\ExportPro\Api\Export;

use Amasty\ExportCore\Api\ExportProcessInterface;

interface NotifierInterface
{
    /**
     * Perform notification process.
     *
     * @param ExportProcessInterface $exportProcess
     */
    public function notify(ExportProcessInterface $exportProcess): void;
}
