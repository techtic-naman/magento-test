<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use MageWorx\SocialProofBase\Api\Data\CampaignInterface;
use MageWorx\SocialProofBase\Api\Data\CampaignSearchResultInterface;

/**
 * @api
 */
interface CampaignRepositoryInterface
{
    /**
     * Save Campaign.
     *
     * @param CampaignInterface $campaign
     * @return CampaignInterface
     * @throws LocalizedException
     */
    public function save(CampaignInterface $campaign): CampaignInterface;

    /**
     * Retrieve Campaign.
     *
     * @param int $campaignId
     * @return CampaignInterface
     * @throws LocalizedException
     */
    public function getById($campaignId): CampaignInterface;

    /**
     * Retrieve Campaigns matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return CampaignSearchResultInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): CampaignSearchResultInterface;

    /**
     * Delete Campaign.
     *
     * @param CampaignInterface $campaign
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(CampaignInterface $campaign): bool;

    /**
     * Delete Campaign by ID.
     *
     * @param int $campaignId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($campaignId): bool;
}
