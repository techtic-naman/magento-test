<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Model;

class QuoteTriggerSetter
{
    /**
     * @var \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface
     */
    protected $customerBalanceRepository;

    /**
     * QuoteTriggerSetter constructor.
     *
     * @param \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface $customerBalanceRepository
     */
    public function __construct(
        \MageWorx\RewardPoints\Api\CustomerBalanceRepositoryInterface $customerBalanceRepository
    ) {
        $this->customerBalanceRepository = $customerBalanceRepository;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param bool $useMwRewardPoints
     * @param null|double $amount
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setQuoteData($quote, $useMwRewardPoints, $amount = null)
    {
        if (!$quote) {
            return false;
        }

        if (!$quote->getCustomerId()) {
            return false;
        }

        if ($quote->getBaseGrandTotal() + $quote->getBaseMwRwrdpointsCurAmnt() <= 0) {
            return false;
        }

        $quote->setUseMwRewardPoints($useMwRewardPoints);

        if ($useMwRewardPoints) {

            $customerBalance = $this->customerBalanceRepository->getByCustomer(
                $quote->getCustomer()->getId(),
                $quote->getStore()->getWebsiteId()
            );

            if ($customerBalance->getId() && $customerBalance->getPoints()) {

                $quote->setRewardPointsCustomerBalance($customerBalance);

                if ($amount && $customerBalance->getPoints() >= $amount) {
                    $quote->setMwRequestedPoints($amount);
                }

                if (!$amount) {
                    $quote->setMwRequestedPoints(null);
                }

                if (!$quote->getPayment()->getMethod()) {
                    $quote->getPayment()->setMethod(\Magento\Payment\Model\Method\Free::PAYMENT_METHOD_FREE_CODE);
                }
            } else {
                $quote->setUseMwRewardPoints(false);
                $quote->setMwRequestedPoints(0);
            }
        } else {
            $quote->setMwRequestedPoints(0);
        }

        return true;
    }
}
