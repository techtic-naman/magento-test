<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Cron;

use Magento\Framework\Event\ManagerInterface as EventManagerInterface;

class AssignByCondition
{
    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * AssignByCondition constructor.
     *
     * @param EventManagerInterface $eventManager
     */
    public function __construct(EventManagerInterface $eventManager)
    {
        $this->eventManager = $eventManager;
    }

    /**
     * @return void
     */
    public function assignByCondition(): void
    {
        $this->eventManager->dispatch('mageworx_countdowntimersbase_assign_by_cron');
    }
}