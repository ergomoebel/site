<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


declare(strict_types=1);

namespace Amasty\OrderExport\Controller\Adminhtml\Profile;

use Amasty\OrderExport\Model\Profile\ProfileRunner;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;

class Export extends Action
{
    const ADMIN_RESOURCE = 'Amasty_OrderExport::order_export_profiles';

    /**
     * @var ProfileRunner
     */
    private $profileRunner;

    public function __construct(
        ProfileRunner $profileRunner,
        Action\Context $context
    ) {
        parent::__construct($context);
        $this->profileRunner = $profileRunner;
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

        try {
            $resultData['processIdentity'] = $this->profileRunner->manualRun($profileId);
        } catch (LocalizedException $e) {
            $resultData['error'] = $e->getMessage();
        }

        $resultJson->setData($resultData);

        return $resultJson;
    }
}
