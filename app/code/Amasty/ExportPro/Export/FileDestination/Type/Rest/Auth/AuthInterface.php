<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */

declare(strict_types=1);

namespace Amasty\ExportPro\Export\FileDestination\Type\Rest\Auth;

use Amasty\ExportCore\Api\ExportProcessInterface;
use Magento\Framework\HTTP\ClientInterface;

interface AuthInterface
{
    /**
     * @param ExportProcessInterface $exportProcess
     * @param ClientInterface $curl
     *
     * @return void
     */
    public function process(ExportProcessInterface  $exportProcess, ClientInterface $curl);
}
