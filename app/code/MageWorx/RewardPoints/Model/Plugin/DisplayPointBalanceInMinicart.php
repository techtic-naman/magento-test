<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\RewardPoints\Model\Plugin;

class DisplayPointBalanceInMinicart {

    /**
     * @var \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface
     */
    protected $customerBalanceRepository;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;

    /**
     * @var \MageWorx\RewardPoints\Helper\Price
     */
    protected $helperPrice;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * DisplayPointBalanceInMinicart constructor.
     *
     * @param \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface $customerBalanceRepository
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param \MageWorx\RewardPoints\Helper\Price $helperPrice
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface $customerBalanceRepository,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \MageWorx\RewardPoints\Helper\Price $helperPrice,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->customerBalanceRepository = $customerBalanceRepository;
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->helperData = $helperData;
        $this->helperPrice = $helperPrice;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Checkout\CustomerData\Cart $subject
     * @param array $result
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterGetSectionData(\Magento\Checkout\CustomerData\Cart $subject, array $result)
    {
        if ($this->helperData->isEnableForCustomer() && $this->helperData->isDisplayMinicartPointBalanceMessage()) {
            $customerId = $this->customerSession->getCustomerId();
            $websiteId = $this->storeManager->getStore()->getWebsiteId();
            $storeId = $this->storeManager->getStore()->getId();

            if ($customerId) {
                $points = $this->customerBalanceRepository->getByCustomer($customerId, $websiteId)->getPoints();
                $message = $this->helperPrice->getFormattedPointBalanceMessage($points, $storeId);
                $result['mageworx_rewardpoints_balance_message'] = $message;
            }
        }

        return $result;
    }
}





