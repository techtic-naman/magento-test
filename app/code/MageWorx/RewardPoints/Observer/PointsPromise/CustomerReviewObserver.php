<?php
/**
 *
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Observer\PointsPromise;

use Magento\Framework\Event\ObserverInterface;


class CustomerReviewObserver implements ObserverInterface
{
    /**
     * 
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;

    /**
     * @var \MageWorx\RewardPoints\Model\ResourceModel\Rule\CollectionFactory
     */
    protected $ruleCollectionFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * CustomerReviewObserver constructor.
     *
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param \MageWorx\RewardPoints\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \MageWorx\RewardPoints\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->helperData = $helperData;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helperData->isEnable()) {
            return;
        }

        /* @var $container \Magento\Review\Model\Review */
        $container = $observer->getEvent()->getContainer();

        if (!$container) {
            return;
        }

        $ruleCollection = $this->ruleCollectionFactory->create();
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $customerGroupId = $this->customerSession->getCustomerGroupId();

        $ruleCollection->addWebsiteGroupDateFilter($websiteId, $customerGroupId);
        $ruleCollection->addIsActiveFilter();
        $ruleCollection->addEventFilter(\MageWorx\RewardPoints\Model\Rule::CUSTOMER_REVIEW_EVENT);
        $ruleCollection->addSortOrder();

        $reward = 0;

        /** @var \MageWorx\RewardPoints\Model\Rule $rule */
        foreach ($ruleCollection->getItems() as $rule) {
            $reward += $rule->getPointsAmount();

            if ($rule->getStopRulesProcessing()) {
                break;
            }
        }

        $container->setReward($reward);
    }
}
