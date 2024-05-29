<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;

class UpcomingPoints implements SectionSourceInterface
{
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
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;

    /**
     * UpcomingPoints constructor.
     *
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param \MageWorx\RewardPoints\Helper\Price $helperPrice
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \MageWorx\RewardPoints\Helper\Price $helperPrice,
        \Magento\Framework\Serialize\Serializer\Json $serializer
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->helperData = $helperData;
        $this->helperPrice = $helperPrice;
        $this->serializer = $serializer;
    }
    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        $allowDisplay = $this->helperData->isDisplayUpcomingPoints() && $this->helperData->isEnableForCustomer();

        if ($allowDisplay) {

            $quote = $this->checkoutSession->getQuote();

            if ($quote) {

                $appliedRewardRulesAsString = $quote->getMwEarnPointsData();

                if ($appliedRewardRulesAsString) {

                    try {
                        $appliedRewardRuleData = $this->serializer->unserialize($appliedRewardRulesAsString);
                    } catch (\Exception $e) {
                        return $this->getFormedData();
                    }

                    $pointsAmount = \array_sum($appliedRewardRuleData);

                    if ($pointsAmount) {

                        $message = $this->helperPrice->getFormattedUpcomingPointsMessage(
                            $pointsAmount,
                            null,
                            'header',
                            true
                        );

                        $enable  = (int)($allowDisplay && $pointsAmount && $message);

                        return $this->getFormedData($enable, $pointsAmount, $message);
                    }
                }
            }
        }

        return $this->getFormedData();
    }

    /**
     * @param int $enable
     * @param double $points
     * @param string $message
     * @return array
     */
    protected function getFormedData($enable = 0, $points = 0, $message = '')
    {
        return [
            'enable' => $enable,
            'points' => $points,
            'message' => $message
        ];
    }
}
