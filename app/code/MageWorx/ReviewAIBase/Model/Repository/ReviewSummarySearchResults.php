<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Model\Repository;

use MageWorx\ReviewAIBase\Api\Data\ReviewSummarySearchResultsInterface;

class ReviewSummarySearchResults extends \Magento\Framework\Api\SearchResults
    implements ReviewSummarySearchResultsInterface
{
    /**
     * @return \Magento\Framework\Api\AbstractExtensibleObject[]|\MageWorx\ReviewAIBase\Api\Data\ReviewSummaryInterface[]
     */
    public function getItems(): array
    {
        return parent::getItems();
    }
}
