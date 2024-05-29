<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * @api
 */
interface CampaignSearchResultInterface extends SearchResultsInterface
{
    /**
     * Get Campaign list.
     *
     * @return CampaignInterface[]
     */
    public function getItems(): array;

    /**
     * Set Campaign list.
     *
     * @param CampaignInterface[] $items
     * @return $this
     */
    public function setItems(array $items): CampaignSearchResultInterface;
}
