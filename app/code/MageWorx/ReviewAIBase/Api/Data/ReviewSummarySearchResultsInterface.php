<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\ReviewAIBase\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface ReviewSummarySearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get the list of review summaries.
     *
     * @return \MageWorx\ReviewAIBase\Api\Data\ReviewSummaryInterface[]
     */
    public function getItems();

    /**
     * Set the list of review summaries.
     *
     * @param \MageWorx\ReviewAIBase\Api\Data\ReviewSummaryInterface[] $items
     * @return $this
     */
    public function setItems(array $items);

    /**
     * Get the total count of review summaries.
     *
     * @return int
     */
    public function getTotalCount();

    /**
     * Set the total count of review summaries.
     *
     * @param int $totalCount
     * @return $this
     */
    public function setTotalCount($totalCount);
}
