<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Model\ResourceModel\CountdownTimer;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use MageWorx\CountdownTimersBase\Model\CountdownTimer\Template as TemplateModel;

class Template extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(
            \MageWorx\CountdownTimersBase\Model\ResourceModel\CountdownTimer::COUNTDOWN_TIMER_TEMPLATE_TABLE,
            TemplateModel::TEMPLATE_ID
        );
    }
}
