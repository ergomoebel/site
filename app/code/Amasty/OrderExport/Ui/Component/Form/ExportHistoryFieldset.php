<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderExport
 */


declare(strict_types=1);

namespace Amasty\OrderExport\Ui\Component\Form;

use Amasty\OrderExport\Model\Profile\Profile;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Form\Fieldset;

class ExportHistoryFieldset extends Fieldset
{
    public function __construct(
        ContextInterface $context,
        array $components = [],
        array $data = []
    ) {
        $componentVisible = $context->getRequestParam(Profile::ID) ? true : false;

        if (isset($data['config']) && !$componentVisible) {
            $data['config']['componentDisabled'] = true;
        }

        parent::__construct($context, $components, $data);
    }
}
