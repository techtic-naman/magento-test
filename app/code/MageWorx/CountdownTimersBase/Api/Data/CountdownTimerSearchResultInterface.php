<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @api
 */
interface CountdownTimerSearchResultInterface extends SearchResultsInterface
{
    /**
     * Get Countdown Timer list.
     *
     * @return CountdownTimerInterface[]
     */
    public function getItems(): array;

    /**
     * Set Countdown Timer list.
     *
     * @param CountdownTimerInterface[] $items
     * @return $this
     */
    public function setItems(array $items): CountdownTimerSearchResultInterface;
}
