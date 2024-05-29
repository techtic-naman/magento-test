<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Model;

use MageWorx\CountdownTimersBase\Api\CountdownTimerRepositoryInterface;
use MageWorx\CountdownTimersBase\Api\Data\CountdownTimerInterface;
use MageWorx\CountdownTimersBase\Api\Data\CountdownTimerSearchResultInterface;
use MageWorx\CountdownTimersBase\Api\Data\CountdownTimerInterfaceFactory;
use MageWorx\CountdownTimersBase\Api\Data\CountdownTimerSearchResultInterfaceFactory;
use MageWorx\CountdownTimersBase\Model\ResourceModel\CountdownTimer as CountdownTimerResourceModel;
use MageWorx\CountdownTimersBase\Model\ResourceModel\CountdownTimer\Collection;
use MageWorx\CountdownTimersBase\Model\ResourceModel\CountdownTimer\CollectionFactory as TimerCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Exception\LocalizedException;

class CountdownTimerRepository implements CountdownTimerRepositoryInterface
{
    /**
     * Cached instances
     *
     * @var array
     */
    protected $instances = [];

    /**
     * Countdown Timer resource model
     *
     * @var CountdownTimerResourceModel
     */
    protected $resource;

    /**
     * Countdown Timer collection factory
     *
     * @var TimerCollectionFactory
     */
    protected $countdownTimerCollectionFactory;

    /**
     * Countdown Timer interface factory
     *
     * @var CountdownTimerInterfaceFactory
     */
    protected $countdownTimerInterfaceFactory;

    /**
     * Data Object Helper
     *
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * Search result factory
     *
     * @var CountdownTimerSearchResultInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * CountdownTimerRepository constructor.
     *
     * @param CountdownTimerResourceModel $resource
     * @param TimerCollectionFactory $countdownTimerCollectionFactory
     * @param CountdownTimerInterfaceFactory $countdownTimerInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param CountdownTimerSearchResultInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        CountdownTimerResourceModel $resource,
        TimerCollectionFactory $countdownTimerCollectionFactory,
        CountdownTimerInterfaceFactory $countdownTimerInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        CountdownTimerSearchResultInterfaceFactory $searchResultsFactory
    ) {
        $this->resource                        = $resource;
        $this->countdownTimerCollectionFactory = $countdownTimerCollectionFactory;
        $this->countdownTimerInterfaceFactory  = $countdownTimerInterfaceFactory;
        $this->dataObjectHelper                = $dataObjectHelper;
        $this->searchResultsFactory            = $searchResultsFactory;
    }

    /**
     * Save Countdown Timer
     *
     * @param CountdownTimerInterface $countdownTimer
     * @return CountdownTimerInterface
     * @throws LocalizedException
     */
    public function save(CountdownTimerInterface $countdownTimer): CountdownTimerInterface
    {
        /** @var CountdownTimerInterface|\Magento\Framework\Model\AbstractModel $countdownTimer */
        try {
            $this->resource->save($countdownTimer);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'Could not save the Countdown Timer: %1',
                    $exception->getMessage()
                )
            );
        }

        return $countdownTimer;
    }

    /**
     * Retrieve Countdown Timer
     *
     * @param int $countdownTimerId
     * @return CountdownTimerInterface
     * @throws NoSuchEntityException
     */
    public function getById($countdownTimerId): CountdownTimerInterface
    {
        if (!isset($this->instances[$countdownTimerId])) {
            /** @var CountdownTimerInterface|\Magento\Framework\Model\AbstractModel $countdownTimer */
            $countdownTimer = $this->countdownTimerInterfaceFactory->create();

            $this->resource->load($countdownTimer, $countdownTimerId);

            if (!$countdownTimer->getId()) {
                throw new NoSuchEntityException(__('Requested Countdown Timer doesn\'t exist'));
            }
            $this->instances[$countdownTimerId] = $countdownTimer;
        }

        return $this->instances[$countdownTimerId];
    }

    /**
     * Retrieve Countdown Timers matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return CountdownTimerSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): CountdownTimerSearchResultInterface
    {
        /** @var CountdownTimerSearchResultInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var Collection $collection */
        $collection = $this->countdownTimerCollectionFactory->create();

        /** @var FilterGroup $group */
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }

        $sortOrders = $searchCriteria->getSortOrders();

        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($searchCriteria->getSortOrders() as $sortOrder) {
                $field = $sortOrder->getField();

                $collection->addOrder(
                    $field,
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        } else {
            $field = CountdownTimerInterface::COUNTDOWN_TIMER_ID;
            $collection->addOrder($field, 'ASC');
        }

        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        /** @var CountdownTimerInterface[] $countdownTimers */
        $countdownTimers = [];
        /** @var \MageWorx\CountdownTimersBase\Model\CountdownTimer $countdownTimer */
        foreach ($collection as $countdownTimer) {
            /** @var CountdownTimerInterface $countdownTimerDataObject */
            $countdownTimerDataObject = $this->countdownTimerInterfaceFactory->create();

            $this->dataObjectHelper->populateWithArray(
                $countdownTimerDataObject,
                $countdownTimer->getData(),
                CountdownTimerInterface::class
            );
            $countdownTimers[] = $countdownTimerDataObject;
        }

        $searchResults->setTotalCount($collection->getSize());

        return $searchResults->setItems($countdownTimers);
    }

    /**
     * Delete Countdown Timer
     *
     * @param CountdownTimerInterface $countdownTimer
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(CountdownTimerInterface $countdownTimer): bool
    {
        /** @var CountdownTimerInterface|\Magento\Framework\Model\AbstractModel $countdownTimer */
        $id = $countdownTimer->getId();

        try {
            unset($this->instances[$id]);
            $this->resource->delete($countdownTimer);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to remove Countdown Timer %1', $id)
            );
        }

        return true;
    }

    /**
     * @param int $countdownTimerId
     * @return bool true on success
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function deleteById($countdownTimerId): bool
    {
        $countdownTimer = $this->getById($countdownTimerId);

        return $this->delete($countdownTimer);
    }

    /**
     * Helper function that adds a FilterGroup to the collection
     *
     * @param FilterGroup $filterGroup
     * @param Collection $collection
     * @return CountdownTimerRepository
     */
    protected function addFilterGroupToCollection(
        FilterGroup $filterGroup,
        Collection $collection
    ): CountdownTimerRepository {
        $filters    = $filterGroup->getFilters();
        $conditions = [];
        $fields     = [];

        foreach ($filters as $filter) {
            $condition    = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $fields[]     = $filter->getField();
            $conditions[] = [$condition => $filter->getValue()];
        }

        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }

        return $this;
    }
}
