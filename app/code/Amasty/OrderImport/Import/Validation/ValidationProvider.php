<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_OrderImport
 */

declare(strict_types=1);

namespace Amasty\OrderImport\Import\Validation;

use Amasty\ImportCore\Api\ImportProcessInterface;
use Amasty\ImportCore\Api\Validation\FieldValidatorInterface;
use Amasty\ImportCore\Api\Validation\ValidationProviderInterface;
use Amasty\ImportCore\Import\Action\DataPrepare\Validation\FieldValidator;
use Amasty\ImportCore\Import\Action\DataPrepare\Validation\FieldValidatorFactory;
use Amasty\ImportCore\Import\Validation\ValueValidator\NotEmpty;
use Amasty\ImportExportCore\Api\Config\ConfigClass\ConfigClassInterfaceFactory;
use Amasty\ImportExportCore\Config\ConfigClass\Factory as ConfigClassFactory;
use Amasty\OrderImport\Import\Action\Import\Customer\CustomerActions;
use Amasty\OrderImport\Model\OptionSource\CustomerMode;

class ValidationProvider implements ValidationProviderInterface
{
    /**
     * @var array|null
     */
    private $customerFieldValidators;

    /**
     * @var ConfigClassFactory
     */
    private $configClassFactory;

    /**
     * @var FieldValidatorFactory
     */
    private $fieldValidatorFactory;

    /**
     * @var ConfigClassInterfaceFactory
     */
    private $configFactory;

    public function __construct(
        ConfigClassFactory $configClassFactory,
        FieldValidatorFactory $fieldValidatorFactory,
        ConfigClassInterfaceFactory $configFactory
    ) {
        $this->configClassFactory = $configClassFactory;
        $this->fieldValidatorFactory = $fieldValidatorFactory;
        $this->configFactory = $configFactory;
    }

    public function getFieldValidators(
        ImportProcessInterface $importProcess,
        array &$validatorsForCollect = []
    ): array {
        $profileConfig = $importProcess->getProfileConfig();
        $behavior = $profileConfig->getBehavior();
        $profileExtension = $profileConfig->getExtensionAttributes();

        if (in_array($behavior, CustomerActions::BEHAVIOR_CODES)
            && (int)$profileExtension->getCustomerMode()
        ) {
            unset($validatorsForCollect['sales_order']['customer_id']);
        }

        if ($profileExtension
            && $profileExtension->getCustomerMode() === CustomerMode::CREATE_CUSTOMER
        ) {
            $validatorsForCollect['sales_order'] = array_merge(
                $validatorsForCollect['sales_order'],
                $this->getCreateCustomerValidators()
            );
        }

        return $validatorsForCollect;
    }

    //phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedFunction
    public function getRowValidators(
        ImportProcessInterface $importProcess,
        array &$validatorsForCollect = []
    ): array {
        return $validatorsForCollect;
    }

    //phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedFunction
    public function getRelationValidators(
        ImportProcessInterface $importProcess,
        array &$validatorsForCollect = []
    ): array {
        return $validatorsForCollect;
    }

    private function getCreateCustomerValidators(): array
    {
        if ($this->customerFieldValidators !== null) {
            return $this->customerFieldValidators;
        }
        $notEmptyValidator = $this->createFieldValidator(
            NotEmpty::class,
            __('Field %1 can\'t be empty for selected Customer Mode.')->render()
        );
        $this->customerFieldValidators['customer_lastname'][] = $notEmptyValidator;
        $this->customerFieldValidators['customer_firstname'][] = $notEmptyValidator;

        return $this->customerFieldValidators;
    }

    private function createFieldValidator(string $name, string $error): FieldValidator
    {
        $config = $this->configFactory->create([
            'baseType'  => FieldValidatorInterface::class,
            'name'      => $name,
            'arguments' => []
        ]);
        /** @var FieldValidator $validator */
        $validator = $this->configClassFactory->createObject(
            $config
        );

        return $this->fieldValidatorFactory->create(
            [
                'validator' => $validator,
                'errorMessage' => $error
            ]
        );
    }
}
