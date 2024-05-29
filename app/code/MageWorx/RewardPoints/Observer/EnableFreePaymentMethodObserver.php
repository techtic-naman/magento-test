<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Observer;

use Magento\Framework\Event\ObserverInterface;

class EnableFreePaymentMethodObserver implements ObserverInterface
{
    /**
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;

    /**
     * EnableFreePaymentMethodObserver constructor.
     *
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     */
    public function __construct(
        \MageWorx\RewardPoints\Helper\Data $helperData
    ) {
        $this->helperData = $helperData;
    }

    /**
     * Force Zero Subtotal Checkout if the grand total is completely covered by RP
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getQuote();
        if (!$quote) {
            return;
        }

        if (!$this->helperData->isEnableForCustomer($quote->getCustomer(), $quote->getStoreId())) {
            return $this;
        }

        /** @var $customerBalance \MageWorx\RewardPoints\Api\Data\CustomerBalanceInterface */
        $customerBalance = $quote->getRewardPointsCustomerBalance();
        if (!$customerBalance || !$customerBalance->getId()) {
            return $this;
        }
        if ($quote->getBaseGrandTotal() == 0
            && (double)$quote->getMwRwrdpointsAmnt()
            && (bool)$quote->getUseMwRewardPoints()
        ) {
            $paymentCode = $observer->getEvent()->getMethodInstance()->getCode();

            /** @var \Magento\Framework\DataObject $result */
            $result = $observer->getEvent()->getResult();
            $result->setData(
                'is_available',
                $paymentCode === \Magento\Payment\Model\Method\Free::PAYMENT_METHOD_FREE_CODE
            );
        }

        return $this;
    }
}
