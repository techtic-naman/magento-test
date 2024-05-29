<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Model\Campaign;

use Magento\Framework\Model\AbstractModel;

class Template extends AbstractModel
{
    const TEMPLATE_ID  = 'template_id';
    const IDENTIFIER   = 'identifier';
    const TITLE        = 'title';
    const CONTENT      = 'content';
    const DISPLAY_MODE = 'display_mode';
    const EVENT_TYPE   = 'event_type';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageworx_socialproofbase_campaign_template';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'campaign_template';
}
