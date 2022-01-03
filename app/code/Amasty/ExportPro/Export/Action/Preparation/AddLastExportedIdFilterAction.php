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
use Amasty\ExportPro\Model\LastExportedId\Repository;

class AddLastExportedIdFilterAction implements ActionInterface
{
    /**
     * @var Repository
     */
    private $lastExportedIdRepository;

    public function __construct(
        Repository $lastExportedIdRepository
    ) {
        $this->lastExportedIdRepository = $lastExportedIdRepository;
    }

    public function initialize(ExportProcessInterface $exportProcess)
    {
        if ($exportProcess->getProfileConfig()->getExtensionAttributes()->getExportNewEntities()) {
            $this->addLastExportedIdFilter($exportProcess);
        }
    }

    //phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedFunction
    public function execute(ExportProcessInterface $exportProcess)
    {
    }

    protected function addLastExportedIdFilter(ExportProcessInterface $exportProcess): ActionInterface
    {
        $profileConfig = $exportProcess->getProfileConfig();
        $externalId = $profileConfig->getExtensionAttributes()->getExternalId();
        $exportModule = $profileConfig->getModuleType();
        $lastExportedId = $this->lastExportedIdRepository->getByTypeAndExternalId($exportModule, $externalId);
        $lastId = (int)$lastExportedId->getLastExportedId();
        if ($lastId) {
            if (!($idFieldName = $exportProcess->getCollection()->getIdFieldName())) {
                $idFieldName = $exportProcess->getCollection()->getNewEmptyItem()->getIdFieldName();
            }
            if ($idFieldName) {
                $exportProcess->getCollection()->addFieldToFilter($idFieldName, ['gt' => $lastId]);
            }
        }

        return $this;
    }
}
