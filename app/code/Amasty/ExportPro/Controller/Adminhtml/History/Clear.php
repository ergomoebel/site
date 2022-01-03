<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


namespace Amasty\ExportPro\Controller\Adminhtml\History;

use Amasty\ExportPro\Model\History\Repository;
use Amasty\ExportPro\Model\ModuleType;
use Magento\Backend\App\Action;

class Clear extends Action
{
    const ADMIN_RESOURCE = 'Amasty_ExportPro::export_history_clear';

    /**
     * @var Repository
     */
    private $repository;

    public function __construct(
        Action\Context $context,
        Repository $repository
    ) {
        parent::__construct($context);
        $this->repository = $repository;
    }

    public function execute()
    {
        $result = $this->repository->clearHistory(ModuleType::TYPE);

        if ($result) {
            $this->messageManager->addSuccessMessage(__("History has been cleared."));
        } else {
            $this->messageManager->addErrorMessage(__("Something went wrong."));
        }

        $this->_redirect('*/*/');
    }
}
