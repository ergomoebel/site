<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */


namespace Amasty\ImportPro\Api\Import;

use Amasty\ImportCore\Api\ImportProcessInterface;

interface NotifierInterface
{
    /**
     * Perform notification process.
     *
     * @param ImportProcessInterface $importProcess
     */
    public function notify(ImportProcessInterface $importProcess): void;
}
