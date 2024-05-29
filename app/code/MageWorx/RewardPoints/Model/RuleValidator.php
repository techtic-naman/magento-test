<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Bundle\Model\Product\Type as Bundle;

class RuleValidator extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $adminQuoteSession;

    /**
     * @var \MageWorx\RewardPoints\Model\ResourceModel\CustomerBalance\CollectionFactory
     */
    protected $rewardPointsEarnRuleCollectionFactory;

    /**
     * @var \MageWorx\RewardPoints\Model\Rule\CalculatorInterface
     */
    protected $calculator;

    /**
     * EarnPoints constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Backend\Model\Session\Quote $adminQuoteSession
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \MageWorx\RewardPoints\Model\ResourceModel\Rule\CollectionFactory $rewardPointsEarnRuleCollectionFactory
     * @param Rule\Calculator $calculator
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\State $appState,
        \Magento\Backend\Model\Session\Quote $adminQuoteSession,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \MageWorx\RewardPoints\Model\ResourceModel\Rule\CollectionFactory $rewardPointsEarnRuleCollectionFactory,
        \MageWorx\RewardPoints\Model\Rule\CalculatorInterface $calculator
    ) {
        $this->customerSession                       = $customerSession;
        $this->checkoutSession                       = $checkoutSession;
        $this->appState                              = $appState;
        $this->adminQuoteSession                     = $adminQuoteSession;
        $this->objectManager                         = $objectManager;
        $this->rewardPointsEarnRuleCollectionFactory = $rewardPointsEarnRuleCollectionFactory;
        $this->calculator                            = $calculator;
        parent::__construct($context);
    }

    /**
     * @param \Magento\Quote\Model\Quote|null $quote
     * @param \Magento\Quote\Model\Quote\Address|null $address
     * @return \MageWorx\RewardPoints\Model\ResourceModel\Rule\Collection
     */
    public function getEarnByOrderRules(
        $quote = null,
        $address = null
    ) {
        $quote   = ($quote !== null) ? $quote : $this->getQuote();
        $address = ($address !== null) ? $address : $this->getSalesAddress($quote);

        /** @var \MageWorx\RewardPoints\Model\ResourceModel\Rule\Collection $ruleCollection */
        $ruleCollection = $this->rewardPointsEarnRuleCollectionFactory->create();

        $ruleCollection
            ->setValidationFilter(
                $quote->getStore()->getWebsiteId(),
                $this->getCustomerGroupId()
            )
            ->addEventFilter(\MageWorx\RewardPoints\Model\Rule::ORDER_PLACED_EARN_EVENT)
            ->addIsActiveFilter()
            ->addSortOrder();

        $isStopRulesProcessing = false;

        /** @var \MageWorx\RewardPoints\Api\Data\RuleInterface $rule */
        foreach ($ruleCollection as $key => $rule) {

            if ($isStopRulesProcessing) {
                $ruleCollection->removeItemByKey($key);
            }

            if ($this->canProcessEarnPointsRule($rule, $address)) {
                if ($rule->getStopRulesProcessing()) {
                    $isStopRulesProcessing = true;
                }
            } else {
                $ruleCollection->removeItemByKey($key);
            }
        }

        return $ruleCollection;
    }

    /**
     * Get customer group id, depend on current checkout session (admin, frontend)
     *
     * @return int
     */
    protected function getCustomerGroupId()
    {
        $areaCode = $this->appState->getAreaCode();
        if ($areaCode == \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE) {
            $customer = $this->objectManager->get('Magento\Backend\Model\Session\Quote')->getQuote()->getCustomer();

            return $customer->getGroupId();
        }

        return $this->customerSession->getCustomerGroupId();
    }

    /**
     * Get current checkout quote
     *
     * @return \Magento\Quote\Model\Quote
     */
    protected function getQuote()
    {
        if ($this->appState->getAreaCode() == \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE) {
            $quote = $this->adminQuoteSession->getQuote();
        } else {
            $quote = $this->checkoutSession->getQuote();
        }

        return $quote;
    }

    /**
     *
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @return mixed
     */
    protected function getSalesAddress($quote)
    {
        $address = $quote->getShippingAddress();
        if (!$address->getSubtotal()) {
            $address = $quote->getBillingAddress();
        }

        return $address;
    }

    /**
     * @param Rule $rule
     * @param \Magento\Quote\Model\Quote\Address $address
     *
     * @return bool
     */
    protected function canProcessEarnPointsRule($rule, $address)
    {
        if ($rule->hasIsValidForAddress($address) && !$address->isObjectNew()) {
            return $rule->getIsValidForAddress($address);
        }

        if (!$rule->validate($address)) {
            $rule->setIsValidForAddress($address, false);

            return false;
        }

        $calculateItems = $this->filterItems($address->getAllItems());

        if (!$rule->hasActionEmptyConditions()) {

            $calculateItems = $this->validateItems($calculateItems, $rule);

            if (!$calculateItems) {
                return false;
            }
        }

        $pointsFromOrder = $this->calculator->calculatePoints(
            $calculateItems,
            $rule,
            $address
        );

        if ($pointsFromOrder <= 0) {
            return false;
        }

        $rule->setCalculatedPoints($pointsFromOrder);
        $rule->setIsValidForAddress($address, true);

        return true;
    }


    protected function filterItems($items)
    {
        $filteredItems = [];

        foreach ($items as $item) {
            if (!$item->getParentItemId()
                && in_array($item->getProductType(), [Configurable::TYPE_CODE, Bundle::TYPE_CODE])
            ) {
                continue;
            }
            $filteredItems[$item->getId()] = $item;
        }

        return $filteredItems;
    }

    /**
     * @param array $items
     * @param Rule $rule
     * @return array
     */
    protected function validateItems($items, $rule)
    {
        $validItems = [];

        foreach ($items as $item) {
            if ($rule->getActions()->validate($item)) {
                $validItems[$item->getId()] = $item;
            }
        }

        return $validItems;
    }

    /**
     * @return \Magento\Backend\Model\Session\Quote|\Magento\Checkout\Model\Session
     */
    protected function getCurrentSession()
    {
        $areaCode = $this->appState->getAreaCode();
        if ($areaCode == \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE) {
            return $this->objectManager->get('Magento\Backend\Model\Session\Quote');
        }

        return $this->checkoutSession;
    }
}
