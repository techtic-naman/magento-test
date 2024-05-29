<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model\Rule\Condition;

class Combine extends \Magento\SalesRule\Model\Rule\Condition\Combine
{
    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\SalesRule\Model\Rule\Condition\Address $conditionAddress
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \MageWorx\RewardPoints\Model\Rule\Condition\Address $conditionAddress,
        array $data = []
    ) {
        parent::__construct($context, $eventManager, $conditionAddress, $data);
        $this->setType(\MageWorx\RewardPoints\Model\Rule\Condition\Combine::class);
    }

    /**
     * Get new child select options
     *
     * Note: in the previous version can be saved conditions from parent class:
     * \Magento\SalesRule\Model\Rule\Condition\Product\Found
     * \Magento\SalesRule\Model\Rule\Condition\Product\Subselect
     * \Magento\SalesRule\Model\Rule\Condition\Combine
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $addressAttributes = $this->_conditionAddress->loadAttributeOptions()->getAttributeOption();
        $attributes = [];
        foreach ($addressAttributes as $code => $label) {
            $attributes[] = [
                'value' => 'Magento\SalesRule\Model\Rule\Condition\Address|' . $code,
                'label' => $label,
            ];
        }

        $conditions = [['value' => '', 'label' => __('Please choose a condition to add.')]];

        $conditions = array_merge_recursive(
            $conditions,
            [
                [
                    'value' => \MageWorx\RewardPoints\Model\Rule\Condition\Product\Found::class,
                    'label' => __('Product attribute combination'),
                ],
                [
                    'value' => \MageWorx\RewardPoints\Model\Rule\Condition\Product\Subselect::class,
                    'label' => __('Products subselection')
                ],
                [
                    'value' => \MageWorx\RewardPoints\Model\Rule\Condition\Combine::class,
                    'label' => __('Conditions combination')
                ],
                ['label' => __('Cart Attribute'), 'value' => $attributes]
            ]
        );

        $additional = new \Magento\Framework\DataObject();
        $this->_eventManager->dispatch('salesrule_rule_condition_combine', ['additional' => $additional]);
        $additionalConditions = $additional->getConditions();
        if ($additionalConditions) {
            $conditions = array_merge_recursive($conditions, $additionalConditions);
        }

        return $conditions;
    }
}
