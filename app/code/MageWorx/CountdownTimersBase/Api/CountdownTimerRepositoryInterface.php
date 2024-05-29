<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use MageWorx\CountdownTimersBase\Api\Data\CountdownTimerInterface;
use MageWorx\CountdownTimersBase\Api\Data\CountdownTimerSearchResultInterface;

interface CountdownTimerRepositoryInterface
{
    /**
     * Save Countdown Timer
     *
     * @param CountdownTimerInterface $countdownTimer
     * @return CountdownTimerInterface
     * @throws LocalizedException
     */
    public function save(CountdownTimerInterface $countdownTimer): CountdownTimerInterface;

    /**
     * Retrieve Countdown Timer by ID
     *
     * @param int $countdownTimerId
     * @return CountdownTimerInterface
     * @throws NoSuchEntityException
     */
    public function getById($countdownTimerId): CountdownTimerInterface;

    /**
     * Retrieve Countdown Timers matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return CountdownTimerSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): CountdownTimerSearchResultInterface;

    /**
     * Delete Countdown Timer
     *
     * @param CountdownTimerInterface $countdownTimer
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(CountdownTimerInterface $countdownTimer): bool;

    /**
     * Delete Countdown Timer by ID
     *
     * @param int $countdownTimerId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($countdownTimerId): bool;
}
