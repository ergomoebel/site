<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


namespace Amasty\ExportPro\Block\Adminhtml\Job\Edit;

use Amasty\ExportPro\Model\Job\Job;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class HistoryButton implements ButtonProviderInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        $jobId = (int)$this->request->getParam(Job::JOB_ID);

        if (!$jobId) {
            return [];
        }

        return [
            'label' => __('History'),
            'class' => 'history',
            'on_click' => '',
            'data_attribute' => [
                'mage-init' => [
                    'Magento_Ui/js/form/button-adapter' => [
                        'actions' => [
                            [
                                'targetName' => 'export_job_form.export_job_form.modal',
                                'actionName' => 'toggleModal',
                            ],
                            [
                                'targetName' => 'index = history_grid',
                                'actionName' => 'render',
                            ]
                        ]
                    ]
                ],
            ],
        ];
    }
}
