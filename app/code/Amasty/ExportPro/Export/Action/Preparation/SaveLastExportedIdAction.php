<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Export\Action\Preparation;

use Amasty\ExportCore\Api\ActionInterface;
use Amasty\ExportCore\Api\ExportProcessInterface;
use Magento\Framework\DB\Select;

class SaveLastExportedIdAction implements ActionInterface
{
    //phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedFunction
    public function initialize(ExportProcessInterface $exportProcess)
    {
    }

    public function execute(ExportProcessInterface $exportProcess)
    {
        if ($exportProcess->getProfileConfig()->getExtensionAttributes()->getExportNewEntities()) {
            $cloneCollection = clone $exportProcess->getCollection();
            $cloneCollection->getSelect()->reset(Select::ORDER);
            $cloneCollection->getSelect()->reset(Select::LIMIT_COUNT);
            $cloneCollection->getSelect()->reset(Select::LIMIT_OFFSET);
            $cloneCollection->setOrder($cloneCollection->getIdFieldName(), $cloneCollection::SORT_ORDER_DESC);
            $cloneCollection->setCurPage(1)->setPageSize(1);
            $lastItemId = $cloneCollection->getFirstItem()->getData($cloneCollection->getIdFieldName());
            $exportProcess->getProfileConfig()
                ->getExtensionAttributes()
                ->setLastExportedId((int)$lastItemId);
        }
    }
}
