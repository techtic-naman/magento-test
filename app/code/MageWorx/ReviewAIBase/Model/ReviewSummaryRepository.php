<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use MageWorx\ReviewAIBase\Api\ReviewSummaryRepositoryInterface;
use MageWorx\ReviewAIBase\Api\Data\ReviewSummaryInterface;
use MageWorx\ReviewAIBase\Model\ResourceModel\ReviewSummary as ReviewSummaryResource;
use MageWorx\ReviewAIBase\Model\ResourceModel\ReviewSummary\Collection as ReviewSummaryCollection;
use MageWorx\ReviewAIBase\Model\ResourceModel\ReviewSummary\CollectionFactory as ReviewSummaryCollectionFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use MageWorx\ReviewAIBase\Api\Data\ReviewSummarySearchResultsInterface as SearchResultsInterface;
use MageWorx\ReviewAIBase\Api\Data\ReviewSummarySearchResultsInterfaceFactory as SearchResultsInterfaceFactory;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SortOrderBuilder;

class ReviewSummaryRepository implements ReviewSummaryRepositoryInterface
{
    /**
     * @var ReviewSummaryResource
     */
    protected ReviewSummaryResource $resource;

    /**
     * @var ReviewSummaryFactory
     */
    protected ReviewSummaryFactory $reviewSummaryFactory;

    /**
     * @var ReviewSummaryCollectionFactory
     */
    protected ReviewSummaryCollectionFactory $reviewSummaryCollectionFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    protected SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var SearchResultsInterfaceFactory
     */
    protected SearchResultsInterfaceFactory $searchResultsFactory;

    /**
     * @var FilterGroupBuilder
     */
    protected FilterGroupBuilder $filterGroupBuilder;

    /**
     * @var SortOrderBuilder
     */
    protected SortOrderBuilder $sortOrderBuilder;

    public function __construct(
        ReviewSummaryResource          $resource,
        ReviewSummaryFactory           $reviewSummaryFactory,
        ReviewSummaryCollectionFactory $reviewSummaryCollectionFactory,
        SearchCriteriaBuilder          $searchCriteriaBuilder,
        SearchResultsInterfaceFactory  $searchResultsFactory,
        FilterGroupBuilder             $filterGroupBuilder,
        SortOrderBuilder               $sortOrderBuilder
    ) {
        $this->resource                       = $resource;
        $this->reviewSummaryFactory           = $reviewSummaryFactory;
        $this->reviewSummaryCollectionFactory = $reviewSummaryCollectionFactory;
        $this->searchCriteriaBuilder          = $searchCriteriaBuilder;
        $this->searchResultsFactory           = $searchResultsFactory;
        $this->filterGroupBuilder             = $filterGroupBuilder;
        $this->sortOrderBuilder               = $sortOrderBuilder;
    }

    /**
     * Save a Review Summary
     *
     * @param ReviewSummaryInterface $reviewSummary
     * @return ReviewSummaryInterface
     * @throws CouldNotSaveException
     */
    public function save(ReviewSummaryInterface $reviewSummary): ReviewSummaryInterface
    {
        try {
            $this->resource->save($reviewSummary);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }
        return $reviewSummary;
    }

    /**
     * Get a Review Summary by ID
     *
     * @param int $entityId
     * @return ReviewSummaryInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $entityId): ReviewSummaryInterface
    {
        $reviewSummary = $this->reviewSummaryFactory->create();
        $this->resource->load($reviewSummary, $entityId);
        if (!$reviewSummary->getId()) {
            throw new NoSuchEntityException(__('Review Summary with ID %1 does not exist', $entityId));
        }
        return $reviewSummary;
    }

    /**
     * Delete a Review Summary
     *
     * @param ReviewSummaryInterface $reviewSummary
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(ReviewSummaryInterface $reviewSummary): bool
    {
        try {
            $this->resource->delete($reviewSummary);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotDeleteException(__($e->getMessage()));
        }
        return true;
    }

    /**
     * Delete a Review Summary by ID
     *
     * @param int $entityId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById(int $entityId): bool
    {
        $reviewSummary = $this->getById($entityId);
        return $this->delete($reviewSummary);
    }

    /**
     * Retrieve review summary matching the specified search criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        $collection = $this->reviewSummaryCollectionFactory->create();
        $this->applySearchCriteria($collection, $searchCriteria);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * Apply search criteria to the collection
     *
     * @param ReviewSummaryCollection $collection
     * @param SearchCriteriaInterface $searchCriteria
     * @return void
     */
    private function applySearchCriteria(
        ReviewSummaryCollection $collection,
        SearchCriteriaInterface $searchCriteria
    ): void {
        // Apply filters
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $field         = $filter->getField();
                $value         = $filter->getValue();
                $conditionType = $filter->getConditionType();
                $collection->addFieldToFilter($field, [$conditionType => $value]);
            }
        }

        // Apply sorting
        foreach ((array)$searchCriteria->getSortOrders() as $sortOrder) {
            $field     = $sortOrder->getField();
            $direction = strtolower($sortOrder->getDirection()) === 'asc' ? 'ASC' : 'DESC';
            $collection->addOrder($field, $direction);
        }

        // Apply pagination
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->setCurPage($searchCriteria->getCurrentPage());
    }
}
