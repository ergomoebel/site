<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerExport
 */


declare(strict_types=1);

namespace Amasty\CustomerExport\Ui\Component\Form;

use Amasty\CustomerExport\Model\Profile\Profile;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Form\Fieldset;

class ExportHistoryFieldset extends Fieldset
{
    public function __construct(
        ContextInterface $context,
        array $components = [],
        array $data = []
    ) {
        $componentVisible = (bool)$context->getRequestParam(Profile::ID);

        if (isset($data['config']) && !$componentVisible) {
            $data['config']['componentDisabled'] = true;
        }

        parent::__construct($context, $components, $data);
    }
}
