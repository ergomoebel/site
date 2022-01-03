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

class DeleteButton implements ButtonProviderInterface
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
        RequestInterface $request,
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
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

        $alertMessage = __('Are you sure you want to do this?');
        $onClick = sprintf(
            'deleteConfirm("%s", "%s")',
            $alertMessage,
            $this->getDeleteUrl($jobId)
        );

        return [
            'label' => __('Delete'),
            'class' => 'delete',
            'on_click' => $onClick,
        ];
    }

    /**
     * @param int $id
     *
     * @return string
     */
    private function getDeleteUrl($id)
    {
        return $this->urlBuilder->getUrl('*/*/delete', [Job::JOB_ID => $id]);
    }
}
