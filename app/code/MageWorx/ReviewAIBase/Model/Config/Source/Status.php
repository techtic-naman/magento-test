<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    const STATUS_UNKNOWN    = 0;
    const STATUS_READY      = 1;
    const STATUS_PENDING    = 2;
    const STATUS_ERROR      = 3;
    const STATUS_DISABLED   = 4;
    const STATUS_PROCESSING = 5;
    const STATUS_IN_QUEUE   = 6;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::STATUS_UNKNOWN, 'label' => __('Unknown')],
            ['value' => self::STATUS_READY, 'label' => __('Ready')],
            ['value' => self::STATUS_PENDING, 'label' => __('Pending')],
            ['value' => self::STATUS_ERROR, 'label' => __('Error')],
            ['value' => self::STATUS_DISABLED, 'label' => __('Disabled')],
            ['value' => self::STATUS_PROCESSING, 'label' => __('Processing')],
            ['value' => self::STATUS_IN_QUEUE, 'label' => __('In Queue')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            self::STATUS_UNKNOWN  => __('Unknown'),
            self::STATUS_READY    => __('Ready'),
            self::STATUS_PENDING  => __('Pending'),
            self::STATUS_ERROR    => __('Error'),
            self::STATUS_DISABLED => __('Disabled'),
            self::STATUS_PROCESSING => __('Processing'),
            self::STATUS_IN_QUEUE => __('In Queue')
        ];
    }
}
