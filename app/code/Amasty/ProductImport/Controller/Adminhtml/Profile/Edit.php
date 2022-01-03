<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductImport
 */


declare(strict_types=1);

namespace Amasty\ProductImport\Controller\Adminhtml\Profile;

use Amasty\ProductImport\Api\ProfileRepositoryInterface;
use Amasty\ProductImport\Model\Profile\Profile;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class Edit extends Action
{
    const ADMIN_RESOURCE = 'Amasty_ProductImport::product_import_profiles';

    /**
     * @var ProfileRepositoryInterface
     */
    private $profileRepository;

    public function __construct(
        Action\Context $context,
        ProfileRepositoryInterface $profileRepository
    ) {
        parent::__construct($context);
        $this->profileRepository = $profileRepository;
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_ProductImport::product_import_profiles');

        if ($profileId = (int)$this->getRequest()->getParam(Profile::ID)) {
            if ($this->profileRepository->getById($profileId)) {
                if ($this->getRequest()->getParam(Duplicate::REQUEST_PARAM_NAME)) {
                    $resultPage->getConfig()->getTitle()->prepend(__('Duplicate Profile'));
                } else {
                    $resultPage->getConfig()->getTitle()->prepend(__('Edit Profile'));
                }
            } else {
                $this->messageManager->addErrorMessage(__('This profile no longer exists.'));

                return $this->_redirect('*/*/index');
            }
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New Profile'));
        }

        return $resultPage;
    }
}
