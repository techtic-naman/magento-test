<?php
/**
 *
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddPointsToQuoteObserver implements ObserverInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;

    /**
     * @var \MageWorx\RewardPoints\Model\RuleValidator
     */
    protected $ruleValidator;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;

    /**
     * AddPointsToQuoteObserver constructor.
     *
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param \MageWorx\RewardPoints\Model\RuleValidator $ruleValidator
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \MageWorx\RewardPoints\Model\RuleValidator $ruleValidator,
        \Magento\Framework\Serialize\Serializer\Json $serializer
    ) {
        $this->storeManager  = $storeManager;
        $this->helperData    = $helperData;
        $this->ruleValidator = $ruleValidator;
        $this->serializer    = $serializer;
    }

    /**
     * Set rewardpoints amount as payment discount
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $quote \Magento\Quote\Model\Quote */
        $quote = $observer->getQuote();

        if ($quote->isVirtual()) {
            $address = $quote->getBillingAddress();
        } else {
            $address = $quote->getShippingAddress();
        }

        if ($this->out($address)) {
            $this->setAppliedRuleData($address, $quote, []);

            return $this;
        }

        $possibleRuleCollection = $this->ruleValidator->getEarnByOrderRules(
            $quote,
            $address
        );

        $appliedRuleData = [];

        foreach ($possibleRuleCollection as $rule) {
            $appliedRuleData[$rule->getId()] = $rule->getCalculatedPoints();
        }

        $this->setAppliedRuleData($address, $quote, $appliedRuleData);

        return $this;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address $address
     * @param \Magento\Quote\Model\Quote $quote
     * @param array $appliedRuleData
     * @return $this
     */
    public function setAppliedRuleData($address, $quote, array $appliedRuleData)
    {
        $appliedRuleDataAsString = $this->serializer->serialize($appliedRuleData);
        $address->setMwEarnPointsData($appliedRuleDataAsString);
        $quote->setMwEarnPointsData($appliedRuleDataAsString);

        return $this;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address $address
     * @return bool
     */
    protected function out($address)
    {
        if (!$this->helperData->isEnableForCustomer($address->getQuote()->getCustomer())) {
            return true;
        }

        if ($address->getSubtotal() == 0) {
            return true;
        }

        $items = $address->getAllItems();
        if (!count($items)) {
            return true;
        }

        return false;
    }
}
