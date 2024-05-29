<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Model\ResourceModel\Campaign;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use MageWorx\SocialProofBase\Model\Campaign\Template as TemplateModel;

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
            \MageWorx\SocialProofBase\Model\ResourceModel\Campaign::CAMPAIGN_TEMPLATE_TABLE,
            TemplateModel::TEMPLATE_ID
        );
    }
}
