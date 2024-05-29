<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\RewardPoints\Model;

use Magento\Framework\ObjectManagerInterface as ObjectManager;

class EventStrategyFactory
{
    const ORDER_PLACED_SPEND_EVENT = 'order_placed_spend';

    const MANUAL_UPDATE_EVENT = 'manual_update';

    const REFUND_EVENT = 'refund_points_increase';

    const REFUND_DECREASE_POINTS_EVENT = 'refund_points_decrease';

    const ORDER_CANCEL_RESTORE_EVENT = 'order_cancel_restore';

    const ORDER_FAILURE_RESTORE_EVENT = 'order_failure_restore';

    const EXPIRE_DATE_EVENT = 'annul_upon_expire_date';

    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager = null;

    /**
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $map;

    /**
     * EventStrategyFactory constructor.
     *
     * @param ObjectManager $objectManager
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param array $map
     */
    public function __construct(
        ObjectManager $objectManager,
        \MageWorx\RewardPoints\Helper\Data $helperData,
        array $map = []
    ) {
        $this->objectManager = $objectManager;
        $this->helperData    = $helperData;
        $this->map           = $map;
    }

    /**
     *
     * @param string $param
     * @param array $arguments
     * @return \MageWorx\RewardPoints\Model\AbstractEventStrategy
     * @throws \UnexpectedValueException
     */
    public function create(string $param, array $arguments = []): \MageWorx\RewardPoints\Model\AbstractEventStrategy
    {
        $map = array_merge($this->getDefaultStrategyClasses(), $this->map);

        if (isset($map[$param])) {
            $instance = $this->objectManager->create($map[$param], $arguments);
        } else {
            $arguments['data'] = ['event_code' => $param];

            $instance = $this->objectManager->create(
                \MageWorx\RewardPoints\Model\EventStrategy\UnknownEventStrategy::class,
                $arguments
            );
        }

        if (!$instance instanceof \MageWorx\RewardPoints\Model\AbstractEventStrategy) {
            throw new \UnexpectedValueException(
                'Class ' . get_class($instance) . ' should be an instance of \MageWorx\RewardPoints\Model\EventStrategy'
            );
        }

        $instance->setEventCode($param);

        return $instance;
    }

    /**
     * @return array
     */
    private function getDefaultStrategyClasses(): array
    {
        $list = [
            Rule::ORDER_PLACED_EARN_EVENT =>
                'MageWorx\RewardPoints\Model\EventStrategy\RuleStrategy\EarnByOrderStrategy',

            Rule::CUSTOMER_NEWSLETTER_SUBSCRIPTION_EVENT =>
                'MageWorx\RewardPoints\Model\EventStrategy\RuleStrategy\NewsletterSubscriptionStrategy',

            Rule::CUSTOMER_BIRTHDAY_EVENT =>
                'MageWorx\RewardPoints\Model\EventStrategy\RuleStrategy\CustomerBirthdayStrategy',

            Rule::CUSTOMER_REGISTRATION_EVENT =>
                'MageWorx\RewardPoints\Model\EventStrategy\RuleStrategy\CustomerRegistrationStrategy',

            Rule::CUSTOMER_REVIEW_EVENT =>
                'MageWorx\RewardPoints\Model\EventStrategy\RuleStrategy\CustomerReviewStrategy',

            self::ORDER_PLACED_SPEND_EVENT =>
                'MageWorx\RewardPoints\Model\EventStrategy\SpendByOrderStrategy',

            self::MANUAL_UPDATE_EVENT =>
                'MageWorx\RewardPoints\Model\EventStrategy\ManualUpdateStrategy',

            self::REFUND_EVENT =>
                'MageWorx\RewardPoints\Model\EventStrategy\RefundStrategy',

            self::REFUND_DECREASE_POINTS_EVENT =>
                'MageWorx\RewardPoints\Model\EventStrategy\RefundPointsDecreaseStrategy',

            self::ORDER_CANCEL_RESTORE_EVENT =>
                'MageWorx\RewardPoints\Model\EventStrategy\RestoreByOrderStrategy',

            self::ORDER_FAILURE_RESTORE_EVENT =>
                'MageWorx\RewardPoints\Model\EventStrategy\RestoreByOrderFailureStrategy',

            self::EXPIRE_DATE_EVENT =>
                'MageWorx\RewardPoints\Model\EventStrategy\AnnulUponExpirationDateStrategy'
        ];

        return $list;
    }
}
