<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


declare(strict_types=1);

namespace Amasty\OrderExport\Controller\Adminhtml\Connection;

use Amasty\OrderExport\Api\Data\ConnectionInterfaceFactory;
use Amasty\OrderExport\Api\ConnectionRepositoryInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Amasty\OrderExport\Ui\DataProvider\Connection\Form;
use Amasty\OrderExport\Model\Connection\Connection;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class Save extends Action
{
    const ADMIN_RESOURCE = 'Amasty_OrderExport::order_export_connections';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ConnectionRepositoryInterface
     */
    private $connectionRepository;

    /**
     * @var ConnectionInterfaceFactory
     */
    private $connectionFactory;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    public function __construct(
        Action\Context $context,
        ConnectionRepositoryInterface $connectionRepository,
        ConnectionInterfaceFactory $connectionFactory,
        LoggerInterface $logger,
        DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->connectionRepository = $connectionRepository;
        $this->connectionFactory = $connectionFactory;
        $this->dataPersistor = $dataPersistor;
    }

    public function execute()
    {
        try {
            if ($data = $this->getRequest()->getPostValue()) {
                if ($connectionId = (int)$this->getRequest()->getParam(Connection::ID)) {
                    $model = $this->connectionRepository->getById($connectionId);
                } else {
                    $model = $this->connectionRepository->getEmptyConnectionModel();
                }
                $model->addData($data);
                $this->connectionRepository->save($model);

                $this->messageManager->addSuccessMessage(__('You saved the connection.'));

                if ($this->getRequest()->getParam('back')) {
                    return $this->_redirect('*/*/edit', [Connection::ID => $model->getConnectionId()]);
                }
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->dataPersistor->set(Form::CONNECTION_DATA, $data);

            if (isset($model) && $model->getConnectionId()) {
                return $this->_redirect('*/*/edit', [Connection::ID => $model->getConnectionId()]);
            } else {
                return $this->_redirect('*/*/edit');
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('An error has occurred'));
            $this->logger->critical($e);

            return $this->_redirect('*/*');
        }

        return $this->_redirect('*/*');
    }
}
