<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Api;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;

interface FrontendCampaignResolverInterface
{
    /**
     * Retrieve Campaign Data Object.
     *
     * @param string $displayMode
     * @param string $pageType
     * @param string $associatedEntityId
     * @param null|int $storeId
     * @param null|int $customerGroupId
     * @param array|null $campaignIds
     * @param bool $excludeCampIds
     * @param array|null $itemIds
     * @param bool $excludeItemIds
     * @return DataObject|null
     * @throws NoSuchEntityException
     */
    public function getCampaign(
        $displayMode,
        $pageType,
        $associatedEntityId,
        $storeId,
        $customerGroupId,
        $campaignIds = null,
        $excludeCampIds = false,
        $itemIds = null,
        $excludeItemIds = true
    ): ?DataObject;
}
