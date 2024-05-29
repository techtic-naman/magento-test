<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Controller\Adminhtml\CountdownTimer;

use MageWorx\CountdownTimersBase\Api\Data\CountdownTimerInterface;
use Magento\Framework\Exception\LocalizedException;

class MassDelete extends \MageWorx\CountdownTimersBase\Controller\Adminhtml\CountdownTimer\MassAction
{
    /**
     * @param CountdownTimerInterface $countdownTimer
     * @throws LocalizedException
     */
    protected function massAction(CountdownTimerInterface $countdownTimer): void
    {
        $this->countdownTimerRepository->delete($countdownTimer);
    }
}
