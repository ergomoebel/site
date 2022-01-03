<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImportPro
 */


namespace Amasty\ImportPro\Block\Adminhtml\Job\Edit;

use Amasty\ImportPro\Model\Job\Job;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class HistoryButton implements ButtonProviderInterface
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

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
                                'targetName' => 'import_job_form.import_job_form.modal',
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
