<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


declare(strict_types=1);

namespace Amasty\OrderExport\Controller\Adminhtml\Connection;

use Amasty\OrderExport\Api\ConnectionRepositoryInterface;
use Amasty\OrderExport\Model\Connection\Connection;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class Delete extends Action
{
    const ADMIN_RESOURCE = 'Amasty_OrderExport::order_export_connections';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ConnectionRepositoryInterface
     */
    private $repository;

    public function __construct(
        Action\Context $context,
        ConnectionRepositoryInterface $repository,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->repository = $repository;
    }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam(Connection::ID);

        if ($id) {
            try {
                $this->repository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('The connection has been deleted.'));
                $this->_redirect('*/*/');

                return;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Can\'t delete the connection right now. Please review the log and try again.')
                );
                $this->logger->critical($e);
            }
            $this->_redirect('*/*/edit', [Connection::ID => $id]);

            return;
        } else {
            $this->messageManager->addErrorMessage(__('Can\'t find a resolution to delete.'));
        }

        $this->_redirect('*/*/');
    }
}
