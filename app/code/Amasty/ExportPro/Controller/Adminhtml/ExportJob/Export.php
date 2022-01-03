<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


declare(strict_types=1);

namespace Amasty\ExportPro\Controller\Adminhtml\ExportJob;

use Amasty\ExportPro\Model\Job\Runner;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;

class Export extends Action
{
    const ADMIN_RESOURCE = 'Amasty_ExportPro::export';

    /**
     * @var Runner
     */
    private $runner;

    public function __construct(
        Runner $runner,
        Action\Context $context
    ) {
        parent::__construct($context);
        $this->runner = $runner;
    }

    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultData = [];

        $jobId = (int)$this->getRequest()->getParam('job_id');
        if (!$jobId) {
            $resultData['error'] = __('Job Id is not set');
        }

        try {
            $resultData['processIdentity'] = $this->runner->manualRun($jobId);
        } catch (LocalizedException $e) {
            $resultData['error'] = $e->getMessage();
        }

        $resultJson->setData($resultData);

        return $resultJson;
    }
}
