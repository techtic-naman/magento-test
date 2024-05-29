<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Controller\Adminhtml\CountdownTimer;

use MageWorx\CountdownTimersBase\Api\Data\CountdownTimerInterface;
use MageWorx\CountdownTimersBase\Model\ResourceModel\CountdownTimer;
use MageWorx\CountdownTimersBase\Model\Source\CountdownTimer\Status as StatusOptions;

class MassDisable extends \MageWorx\CountdownTimersBase\Controller\Adminhtml\CountdownTimer\MassAction
{
    /**
     * @param CountdownTimerInterface $countdownTimer
     */
    protected function massAction(CountdownTimerInterface $countdownTimer): void
    {
        if ((int)$countdownTimer->getStatus() !== StatusOptions::DISABLE) {
            $table      = $this->resourceConnection->getTableName(CountdownTimer::COUNTDOWN_TIMER_TABLE);
            $connection = $this->resourceConnection->getConnection();
            $connection
                ->update(
                    $table,
                    [CountdownTimerInterface::STATUS => StatusOptions::DISABLE],
                    $connection->prepareSqlCondition(
                        CountdownTimerInterface::COUNTDOWN_TIMER_ID,
                        $countdownTimer->getId()
                    )
                );
        }
    }
}
