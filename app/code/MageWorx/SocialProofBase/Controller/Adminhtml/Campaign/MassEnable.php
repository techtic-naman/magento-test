<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Controller\Adminhtml\Campaign;

use MageWorx\SocialProofBase\Api\Data\CampaignInterface;
use MageWorx\SocialProofBase\Model\ResourceModel\Campaign;
use MageWorx\SocialProofBase\Model\Source\Campaign\Status as StatusOptions;

class MassEnable extends \MageWorx\SocialProofBase\Controller\Adminhtml\Campaign\MassAction
{
    /**
     * @param CampaignInterface $campaign
     */
    protected function massAction(CampaignInterface $campaign): void
    {
        if ((int)$campaign->getStatus() !== StatusOptions::ENABLE) {
            $table      = $this->resourceConnection->getTableName(Campaign::CAMPAIGN_TABLE);
            $connection = $this->resourceConnection->getConnection();
            $connection
                ->update(
                    $table,
                    [CampaignInterface::STATUS => StatusOptions::ENABLE],
                    $connection->prepareSqlCondition(CampaignInterface::CAMPAIGN_ID, $campaign->getId())
                );
        }
    }
}
