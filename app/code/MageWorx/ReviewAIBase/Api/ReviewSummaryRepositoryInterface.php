<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace MageWorx\ReviewAIBase\Api;

use MageWorx\ReviewAIBase\Api\Data\ReviewSummaryInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use MageWorx\ReviewAIBase\Api\Data\ReviewSummarySearchResultsInterface as SearchResultsInterface;

interface ReviewSummaryRepositoryInterface
{
    /**
     * Save a Review Summary
     *
     * @param ReviewSummaryInterface $reviewSummary
     * @return ReviewSummaryInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(ReviewSummaryInterface $reviewSummary): ReviewSummaryInterface;

    /**
     * Get a Review Summary by ID
     *
     * @param int $entityId
     * @return ReviewSummaryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $entityId): ReviewSummaryInterface;

    /**
     * Delete a Review Summary
     *
     * @param ReviewSummaryInterface $reviewSummary
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(ReviewSummaryInterface $reviewSummary): bool;

    /**
     * Delete a Review Summary by ID
     *
     * @param int $entityId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById(int $entityId): bool;

    /**
     * Retrieve review summary matching the specified search criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;
}
