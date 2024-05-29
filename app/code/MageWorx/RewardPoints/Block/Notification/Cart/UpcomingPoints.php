<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Block\Notification\Cart;

/**
 * Block to display messages for upcoming reward points
 */
class UpcomingPoints extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;

    /**
     * @var \MageWorx\RewardPoints\Helper\Price
     */
    protected $helperPrice;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

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
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \MageWorx\RewardPoints\Helper\Data $helperData,
        \MageWorx\RewardPoints\Helper\Price $helperPrice,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helperData      = $helperData;
        $this->checkoutSession = $checkoutSession;
        $this->helperPrice     = $helperPrice;
        $this->serializer      = $serializer;
    }

    /**
     * Set template variables
     *
     * @return array
     */
    protected function _prepareTemplateData()
    {
        $allowDisplay = $this->helperData->isDisplayCartUpcomingPoints() && $this->helperData->isEnableForCustomer();

        $data = $this->getFormedData();

        if ($allowDisplay) {

            $quote = $this->checkoutSession->getQuote();

            if ($quote) {

                $appliedRewardRulesAsString = $quote->getMwEarnPointsData();

                if ($appliedRewardRulesAsString) {

                    try {
                        $appliedRewardRuleData = $this->serializer->unserialize($appliedRewardRulesAsString);
                        $pointsAmount          = \array_sum($appliedRewardRuleData);

                        if ($pointsAmount) {
                            $message = $this->helperPrice->getFormattedUpcomingPointsMessage(
                                $pointsAmount,
                                null,
                                'cart'
                            );
                            $enable  = (int)($allowDisplay && $pointsAmount && $message);
                            $data    = $this->getFormedData($enable, $pointsAmount, $message);
                        }
                    } catch (\Exception $e) {
                        $data = $this->getFormedData();
                    }
                }
            }
        }

        $this->addData($data);

        return $data;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->helperData->isDisplayCartUpcomingPoints()) {
            return '';
        }

        $data = $this->_prepareTemplateData();

        if (!$data['enable'] || !$data['points'] || !$data['message']) {
            return '';
        }

        return parent::_toHtml();
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
            'enable'  => $enable,
            'points'  => $points,
            'message' => $message
        ];
    }
}

