<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerImport
 */


declare(strict_types=1);

namespace Amasty\CustomerImport\Ui\Component\Form;

use Amasty\CustomerImport\Model\Profile\Profile;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Form\Fieldset;

class ImportHistoryFieldset extends Fieldset
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
