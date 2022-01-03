<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerImport
 */


declare(strict_types=1);

namespace Amasty\CustomerImport\Controller\Adminhtml\Profile;

use Amasty\ImportPro\Model\History\Repository;
use Amasty\CustomerImport\Api\ProfileRepositoryInterface;
use Amasty\CustomerImport\Model\ModuleType;
use Amasty\CustomerImport\Model\Profile\Profile;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class Delete extends Action
{
    const ADMIN_RESOURCE = 'Amasty_CustomerImport::customer_import_profiles';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ProfileRepositoryInterface
     */
    private $repository;

    /**
     * @var Repository
     */
    private $historyRepository;

    public function __construct(
        Action\Context $context,
        ProfileRepositoryInterface $repository,
        Repository $historyRepository,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->repository = $repository;
        $this->historyRepository = $historyRepository;
    }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam(Profile::ID);

        if ($id) {
            try {
//                $this->historyRepository->clearByJobTypeAndId(ModuleType::TYPE, $id);
                $this->repository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('The profile has been deleted.'));
                $this->_redirect('*/*/');

                return;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Can\'t delete the profile right now. Please review the log and try again.')
                );
                $this->logger->critical($e);
            }
            $this->_redirect('*/*/edit', [Profile::ID => $id]);

            return;
        } else {
            $this->messageManager->addErrorMessage(__('Can\'t find a resolution to delete.'));
        }

        $this->_redirect('*/*/');
    }
}
