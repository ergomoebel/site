<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerImport
 */


declare(strict_types=1);

namespace Amasty\CustomerImport\Controller\Adminhtml\Profile;

use Amasty\ImportCore\Api\Config\ProfileConfigInterface;
use Amasty\CustomerImport\Model\Profile\ProfileRunner;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;

class Import extends Action
{
    const ADMIN_RESOURCE = 'Amasty_CustomerImport::customer_import_profiles';

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
            $strategy = $this->getRequest()->getParam('strategy');
            $setProfileConfigData = function (ProfileConfigInterface $profileConfig) use ($strategy) {
                $profileConfig->setStrategy($strategy);
            };
            $resultData['processIdentity'] = $this->profileRunner->manualRun($profileId, $setProfileConfigData);
        } catch (LocalizedException $e) {
            $resultData['error'] = $e->getMessage();
        }

        $resultJson->setData($resultData);

        return $resultJson;
    }
}
