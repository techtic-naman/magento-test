<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\PersonalPromotion\Plugin;

use Magento\Framework\Data\CollectionFactory;
use MageWorx\PersonalPromotion\Helper\Data as HelperData;
use MageWorx\PersonalPromotion\Helper\LinkFieldResolver as HelperLinkFieldResolver;
use MageWorx\PersonalPromotion\Model\ResourceModel\PersonalPromotion as PersonalPromotionResourceModel;
use MageWorx\PersonalPromotion\Helper\Customer as HelperCustomer;
use Magento\SalesRule\Api\Data\RuleInterface;

class RulesApplier
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var HelperCustomer
     */
    protected $helperCustomer;

    /**
     * @var HelperLinkFieldResolver
     */
    protected $helperLinkFieldResolver;

    /**
     * @var PersonalPromotionResourceModel
     */
    protected $personalPromotionResourceModel;

    /**
     * @var CollectionFactory
     */
    protected $ruleCollectionFactory;

    public function __construct(
        HelperData $helperData,
        HelperCustomer $helperCustomer,
        HelperLinkFieldResolver $helperLinkFieldResolver,
        PersonalPromotionResourceModel $personalPromotionResourceModel,
        CollectionFactory $ruleCollectionFactory
    ) {
        $this->helperData                     = $helperData;
        $this->helperCustomer                 = $helperCustomer;
        $this->helperLinkFieldResolver        = $helperLinkFieldResolver;
        $this->personalPromotionResourceModel = $personalPromotionResourceModel;
        $this->ruleCollectionFactory          = $ruleCollectionFactory;
    }

    /**
     * @param \Magento\SalesRule\Model\RulesApplier $subject
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param \Magento\SalesRule\Model\ResourceModel\Rule\Collection|array $rules //type was changed in magento 2.4.4
     * @param bool $skipValidation
     * @param mixed $couponCode
     * @return array
     * @throws \Exception
     */
    public function beforeApplyRules(
        \Magento\SalesRule\Model\RulesApplier $subject,
        \Magento\Quote\Model\Quote\Item\AbstractItem $item,
        $rules,
        $skipValidation,
        $couponCode
    ) {
        $isArray = is_array($rules);

        if ($isArray) {
            $rules = $this->convertToCollection($rules);
        }

        if ($this->helperData->isEnablePersonalPromotion() && $rules->count()) {

            $linkField = $this->helperLinkFieldResolver->getLinkField(RuleInterface::class);

            $ruleIds = $rules->getColumnValues($linkField);

            $customersRuleIds = $this->personalPromotionResourceModel->getCustomersRuleIds($ruleIds);

            $currentCustomerRuleIds = [];
            $currentCustomerId      = $this->helperCustomer->getCurrentCustomerId();
            if (isset($currentCustomerId)) {
                $currentCustomerRuleIds = $this->personalPromotionResourceModel->getRuleIdsByCustomerId(
                    $currentCustomerId,
                    $linkField
                );
            }

            foreach ($rules as $rule) {
                $ruleId = $rule->getData($linkField);

                // We don't exclude rules without selected customers
                if (in_array($ruleId, $customersRuleIds) && !in_array($ruleId, $currentCustomerRuleIds)) {
                    $rules->removeItemByKey($rule->getId());
                }
            }
        }

        if ($isArray) {
            $rules = $rules->getItems();
        }

        return [$item, $rules, $skipValidation, $couponCode];
    }


    /**
     * @throws \Exception
     * @return \Magento\Framework\Data\Collection
     */
    protected function convertToCollection($rules)
    {
        $collection = $this->ruleCollectionFactory->create();

        foreach ($rules as $rule) {
            $collection->addItem($rule);
        }

        return $collection;
    }
}
