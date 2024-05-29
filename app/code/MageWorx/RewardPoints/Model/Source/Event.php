<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\RewardPoints\Model\Source;

use MageWorx\RewardPoints\Model\Rule;

class Event extends \MageWorx\RewardPoints\Model\Source
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('Placed order'), 'value' => Rule::ORDER_PLACED_EARN_EVENT],
            ['label' => __('Customer birthday'), 'value' => Rule::CUSTOMER_BIRTHDAY_EVENT],
            ['label' => __('Customer registration'), 'value' => Rule::CUSTOMER_REGISTRATION_EVENT],
// Customer Inactivity event will be realized in the follow version
//            ['label' => __('Customer inactivity'), 'value' => Rule::CUSTOMER_INACTIVITY_EVENT],
            ['label' => __('Customer review'), 'value' => Rule::CUSTOMER_REVIEW_EVENT],
            ['label' => __('Newsletter subscription'), 'value' => Rule::CUSTOMER_NEWSLETTER_SUBSCRIPTION_EVENT],
        ];
    }
}

