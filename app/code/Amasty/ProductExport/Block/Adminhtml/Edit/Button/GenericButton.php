<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ProductExport
 */


declare(strict_types=1);

namespace Amasty\ProductExport\Block\Adminhtml\Edit\Button;

use Amasty\ProductExport\Controller\Adminhtml\Profile\Duplicate;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;

class GenericButton
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context
    ) {
        $this->urlBuilder = $context->getUrlBuilder();
        $this->request = $context->getRequest();
    }

    public function getProfileId()
    {
        return $this->request->getParam('id');
    }

    public function getUrl($route = '', $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }

    public function isDuplicate()
    {
        return (bool)$this->request->getParam(Duplicate::REQUEST_PARAM_NAME);
    }
}
