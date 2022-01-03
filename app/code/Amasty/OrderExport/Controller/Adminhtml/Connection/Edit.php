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
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\ResultFactory;

class Edit extends Action
{
    const ADMIN_RESOURCE = 'Amasty_OrderExport::order_export_connections';

    /**
     * @var ConnectionRepositoryInterface
     */
    private $connectionRepository;

    public function __construct(
        Action\Context $context,
        ConnectionRepositoryInterface $connectionRepository
    ) {
        parent::__construct($context);
        $this->connectionRepository = $connectionRepository;
    }

    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Amasty_OrderExport::order_export_connections');

        if ($connectionId = (int)$this->getRequest()->getParam(Connection::ID)) {
            if ($this->connectionRepository->getById($connectionId)) {
                $resultPage->getConfig()->getTitle()->prepend(__('Edit 3rd Party Link'));
            } else {
                $this->messageManager->addErrorMessage(__('This 3rd party link no longer exists.'));

                return $this->_redirect('*/*/index');
            }
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New 3rd Party Link'));
        }

        return $resultPage;
    }
}
