<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Model\CountdownTimer;

use Magento\Framework\Model\AbstractModel;

class Template extends AbstractModel
{
    const TEMPLATE_ID = 'template_id';
    const IDENTIFIER  = 'identifier';
    const TITLE       = 'title';
    const THEME       = 'theme';
    const ACCENT      = 'accent';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageworx_countdowntimersbase_countdowntimer_template';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'countdowntimer_template';
}
