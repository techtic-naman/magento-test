<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Block\Adminhtml\Order\Create;

class RewardPoints extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Sales\Model\AdminOrder\Create
     */
    protected $orderCreate;

    /**
     * @var \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface
     */
    protected $customerBalanceRepository;

    /**
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;

    /**
     * @var \MageWorx\RewardPoints\Helper\Price
     */
    protected $helperPrice;

    /**
     * @var \MageWorx\RewardPoints\Model\CustomerBalance
     */
    protected $loadedCustomerBalance;

    /**
     * RewardPoints constructor.
     *
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface $customerBalanceRepository
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param \MageWorx\RewardPoints\Helper\Price $helperPrice
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface $customerBalanceRepository,
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \MageWorx\RewardPoints\Helper\Price $helperPrice,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->orderCreate = $orderCreate;
        $this->customerBalanceRepository = $customerBalanceRepository;
        $this->helperData = $helperData;
        $this->helperPrice = $helperPrice;

        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->orderCreate->getQuote();
    }

    /**
     * @return bool
     */
    public function canUseRewardPoints()
    {
        if (!$this->helperData->isEnableForCustomer(
            $this->getQuote()->getCustomer(),
            $this->getQuote()->getStoreId()
        )) {
            return false;
        }

        if (!(double)$this->getCustomerBalance()->getCurrencyAmount()) {
            return false;
        }

        if ($this->getQuote()->getBaseGrandTotal() + $this->getQuote()->getBaseMwRwrdpointsCurAmnt() <= 0) {

            return false;
        }

        return true;
    }

    /**
     * @return \MageWorx\RewardPoints\Api\Data\CustomerBalanceInterface|\MageWorx\RewardPoints\Model\CustomerBalance
     */
    public function getCustomerBalance()
    {
        if ($this->loadedCustomerBalance === null) {

            $customerBalance = $this->customerBalanceRepository->getByCustomer(
                $this->getQuote()->getCustomer()->getId(),
                $this->getQuote()->getStore()->getWebsiteId()
            );

            $this->loadedCustomerBalance = $customerBalance;
        }

        return $this->loadedCustomerBalance;
    }

    /**
     * @return bool
     */
    public function appliedRewardPoints()
    {
        return (bool)$this->orderCreate->getQuote()->getUseMwRewardPoints();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTitle()
    {
        return __(
            "Apply Reward Points. Customer balance is %1. Points can be applied for: %2.",
            $this->helperPrice->getFormattedPointsCurrency($this->getCustomerBalance()->getPoints()),
            implode(', ', $this->helperData->getApplyForList())
        );
    }
}
