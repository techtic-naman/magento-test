<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Api;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;

interface FrontendCountdownTimerResolverInterface
{
    /**
     * Retrieve Countdown Timer Data Object.
     *
     * @param int $storeId
     * @param int $customerGroupId
     * @param int $productId
     * @return DataObject|null
     * @throws NoSuchEntityException
     */
    public function getCountdownTimer($storeId, $customerGroupId, $productId): ?DataObject;
}
