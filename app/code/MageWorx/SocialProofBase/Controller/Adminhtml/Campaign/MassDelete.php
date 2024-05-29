<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Controller\Adminhtml\Campaign;

use MageWorx\SocialProofBase\Api\Data\CampaignInterface;
use Magento\Framework\Exception\LocalizedException;

class MassDelete extends \MageWorx\SocialProofBase\Controller\Adminhtml\Campaign\MassAction
{
    /**
     * @param CampaignInterface $campaign
     * @throws LocalizedException
     */
    protected function massAction(CampaignInterface $campaign): void
    {
        $this->campaignRepository->delete($campaign);
    }
}
