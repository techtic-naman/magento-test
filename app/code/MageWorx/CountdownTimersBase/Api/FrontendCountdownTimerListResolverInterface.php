<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Api;

use Magento\Framework\Exception\NoSuchEntityException;

interface FrontendCountdownTimerListResolverInterface
{
    /**
     * @param int $customerGroupId
     * @param array $productIds
     * @return array
     * @throws NoSuchEntityException
     */
    public function getCountdownTimers($customerGroupId, array $productIds): array;
}
