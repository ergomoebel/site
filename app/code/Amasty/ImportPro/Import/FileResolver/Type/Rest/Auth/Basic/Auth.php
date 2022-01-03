<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */

declare(strict_types=1);

namespace Amasty\ImportPro\Import\FileResolver\Type\Rest\Auth\Basic;

use Amasty\ImportCore\Api\ImportProcessInterface;
use Amasty\ImportPro\Import\FileResolver\Type\Rest\Auth\AuthInterface;
use Magento\Framework\HTTP\ClientInterface;

class Auth implements AuthInterface
{
    public function process(ImportProcessInterface $importProcess, ClientInterface $curl)
    {
        if ($importProcess->getProfileConfig()->getExtensionAttributes()->getRestFileResolver()
            && ($config = $importProcess->getProfileConfig()->getExtensionAttributes()
                ->getRestFileResolver()->getExtensionAttributes()->getBasic())
        ) {
            $curl->setCredentials($config->getUsername(), $config->getPassword());
        }
    }
}
