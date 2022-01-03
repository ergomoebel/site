<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


declare(strict_types=1);

namespace Amasty\OrderExport\Controller\Adminhtml\Order;

use Amasty\ExportCore\Api\Config\Profile\FieldFilterInterfaceFactory;
use Amasty\ExportCore\Api\Config\ProfileConfigInterface;
use Amasty\ExportCore\Export\Filter\Type\Select\ConfigInterfaceFactory;
use Amasty\OrderExport\Model\Profile\ProfileRunner;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;

class Export extends Action
{
    const ADMIN_RESOURCE = 'Amasty_OrderExport::order_export_profiles';

    /**
     * @var ProfileRunner
     */
    private $profileRunner;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var CollectionFactory
     */
    private $orderCollectionFactory;

    /**
     * @var FieldFilterInterfaceFactory
     */
    private $filterFactory;

    /**
     * @var ConfigInterfaceFactory
     */
    private $selectFilterConfigFactory;

    public function __construct(
        FieldFilterInterfaceFactory $filterFactory,
        ConfigInterfaceFactory $selectFilterConfigFactory,
        ProfileRunner $profileRunner,
        Filter $filter,
        CollectionFactory $orderCollectionFactory,
        Action\Context $context
    ) {
        parent::__construct($context);
        $this->profileRunner = $profileRunner;
        $this->filter = $filter;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->filterFactory = $filterFactory;
        $this->selectFilterConfigFactory = $selectFilterConfigFactory;
    }

    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultData = [];

        $profileId = (int)$this->getRequest()->getParam('profile_id');
        if (!$profileId) {
            $resultData['error'] = __('Profile Id is not set');
        }

        $ids = [];
        if ($this->getRequest()->getParam(Filter::SELECTED_PARAM) !== 'false') {
            $this->filter->applySelectionOnTargetProvider();
            $collection = $this->filter->getCollection($this->orderCollectionFactory->create());
            $ids = $collection->getAllIds();
        }

        try {
            $addOrderIds = null;
            if (!empty($ids)) {
                $addOrderIds = function (ProfileConfigInterface $profileConfig) use ($ids) {
                    $filters = $profileConfig->getFieldsConfig()->getFilters() ?: [];
                    $filter = $this->filterFactory->create();
                    $filter->setField('entity_id');
                    $filter->setCondition('in');
                    $filter->setType('select');

                    $filterConfig = $this->selectFilterConfigFactory->create();
                    $filterConfig->setValue($ids);
                    $filter->getExtensionAttributes()->setSelectFilter($filterConfig);
                    $filters[] = $filter;
                    $profileConfig->getFieldsConfig()->setFilters($filters);
                };
            }
            $resultData['processIdentity'] = $this->profileRunner->manualRun((int)$profileId, $addOrderIds);
        } catch (LocalizedException $e) {
            $resultData['error'] = $e->getMessage();
        }

        $resultJson->setData($resultData);

        return $resultJson;
    }
}
