<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ExportPro
 */


namespace Amasty\ExportPro\Model\OptionSource\Email;

use Magento\Email\Model\ResourceModel\Template\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\DataObject;

class Template extends DataObject implements OptionSourceInterface
{
    const TEMPLATE_CODE = 'amexportpro_run_export_notification_template';

    /**
     * @var CollectionFactory
     */
    private $templatesFactory;

    public function __construct(
        CollectionFactory $templatesFactory,
        array $data = []
    ) {
        parent::__construct($data);
        $this->templatesFactory = $templatesFactory;
    }

    public function toOptionArray()
    {
        $collection = $this->templatesFactory->create();
        $collection->addFieldToFilter('orig_template_code', self::TEMPLATE_CODE);
        $options = $collection->toOptionArray();
        array_unshift(
            $options,
            ['value' => self::TEMPLATE_CODE, 'label' => __('Amasty Export Pro: Export Run Notification')]
        );

        return $options;
    }
}
