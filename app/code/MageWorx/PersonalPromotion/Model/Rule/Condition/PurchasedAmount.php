<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\PersonalPromotion\Model\Rule\Condition;

use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;
use MageWorx\PersonalPromotion\Model\PurchasesDataProvider;

class PurchasedAmount extends AbstractCondition
{
    /**
     * @var PurchasesDataProvider
     */
    protected $purchasesDataProvider;

    /**
     * PurchasedAmount constructor.
     *
     * @param Context $context
     * @param PurchasesDataProvider $purchasesDataProvider
     * @param array $data
     */
    public function __construct(
        Context $context,
        PurchasesDataProvider $purchasesDataProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->purchasesDataProvider = $purchasesDataProvider;
    }

    /**
     * Get input type
     *
     * @return string
     */
    public function getInputType()
    {
        return 'numeric';
    }

    /**
     * Get value element type
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'text';
    }

    /**
     * Get attribute element
     *
     * @return AbstractCondition
     */
    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);

        return $element;
    }

    /**
     * Load attribute options
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $this->setAttributeOption(
            [
                'purchased_amount' => __('Purchased amount')
            ]
        );

        return $this;
    }

    /**
     * Get value select options
     *
     * @return array
     */
    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            $this->setData('value_select_options', []);
        }

        return $this->getData('value_select_options');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $model
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $customerId = $model->getCustomerId();

        if (!$customerId) {
            return false;
        }

        $attributeValue = $this->purchasesDataProvider->getPurchasedAmountByCustomerId($customerId);

        return $this->validateAttribute($attributeValue);
    }
}