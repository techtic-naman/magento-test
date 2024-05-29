<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Model;

use MageWorx\SocialProofBase\Api\CampaignRepositoryInterface;
use MageWorx\SocialProofBase\Api\Data\CampaignInterface;
use MageWorx\SocialProofBase\Api\Data\CampaignSearchResultInterface;
use MageWorx\SocialProofBase\Api\Data\CampaignInterfaceFactory;
use MageWorx\SocialProofBase\Api\Data\CampaignSearchResultInterfaceFactory;
use MageWorx\SocialProofBase\Model\ResourceModel\Campaign as CampaignResourceModel;
use MageWorx\SocialProofBase\Model\ResourceModel\Campaign\Collection;
use MageWorx\SocialProofBase\Model\ResourceModel\Campaign\CollectionFactory as CampaignCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Exception\LocalizedException;

class CampaignRepository implements CampaignRepositoryInterface
{
    /**
     * Cached instances
     *
     * @var array
     */
    protected $instances = [];

    /**
     * Campaign resource model
     *
     * @var CampaignResourceModel
     */
    protected $resource;

    /**
     * Campaign collection factory
     *
     * @var CampaignCollectionFactory
     */
    protected $campaignCollectionFactory;

    /**
     * Campaign interface factory
     *
     * @var CampaignInterfaceFactory
     */
    protected $campaignInterfaceFactory;

    /**
     * Data Object Helper
     *
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * Search result factory
     *
     * @var CampaignSearchResultInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * CampaignRepository constructor.
     *
     * @param CampaignResourceModel $resource
     * @param CampaignCollectionFactory $campaignCollectionFactory
     * @param CampaignInterfaceFactory $campaignInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param CampaignSearchResultInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        CampaignResourceModel $resource,
        CampaignCollectionFactory $campaignCollectionFactory,
        CampaignInterfaceFactory $campaignInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        CampaignSearchResultInterfaceFactory $searchResultsFactory
    ) {
        $this->resource                  = $resource;
        $this->campaignCollectionFactory = $campaignCollectionFactory;
        $this->campaignInterfaceFactory  = $campaignInterfaceFactory;
        $this->dataObjectHelper          = $dataObjectHelper;
        $this->searchResultsFactory      = $searchResultsFactory;
    }

    /**
     * Save Campaign.
     *
     * @param CampaignInterface $campaign
     * @return CampaignInterface
     * @throws LocalizedException
     */
    public function save(CampaignInterface $campaign): CampaignInterface
    {
        /** @var CampaignInterface|\Magento\Framework\Model\AbstractModel $campaign */
        try {
            $this->resource->save($campaign);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __(
                    'Could not save the Campaign: %1',
                    $exception->getMessage()
                )
            );
        }

        return $campaign;
    }

    /**
     * Retrieve Campaign.
     *
     * @param int $campaignId
     * @return CampaignInterface
     * @throws LocalizedException
     */
    public function getById($campaignId): CampaignInterface
    {
        if (!isset($this->instances[$campaignId])) {
            /** @var CampaignInterface|\Magento\Framework\Model\AbstractModel $campaign */
            $campaign = $this->campaignInterfaceFactory->create();

            $this->resource->load($campaign, $campaignId);

            if (!$campaign->getId()) {
                throw new NoSuchEntityException(__('Requested Campaign doesn\'t exist'));
            }
            $this->instances[$campaignId] = $campaign;
        }

        return $this->instances[$campaignId];
    }

    /**
     * Retrieve Campaigns matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return CampaignSearchResultInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): CampaignSearchResultInterface
    {
        /** @var CampaignSearchResultInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var Collection $collection */
        $collection = $this->campaignCollectionFactory->create();

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
            $field = CampaignInterface::CAMPAIGN_ID;
            $collection->addOrder($field, 'ASC');
        }

        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        /** @var CampaignInterface[] $campaigns */
        $campaigns = [];
        /** @var \MageWorx\SocialProofBase\Model\Campaign $campaign */
        foreach ($collection as $campaign) {
            /** @var CampaignInterface $campaignDataObject */
            $campaignDataObject = $this->campaignInterfaceFactory->create();

            $this->dataObjectHelper->populateWithArray(
                $campaignDataObject,
                $campaign->getData(),
                CampaignInterface::class
            );
            $campaigns[] = $campaignDataObject;
        }

        $searchResults->setTotalCount($collection->getSize());

        return $searchResults->setItems($campaigns);
    }

    /**
     * Delete Campaign.
     *
     * @param CampaignInterface $campaign
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(CampaignInterface $campaign): bool
    {
        /** @var CampaignInterface|\Magento\Framework\Model\AbstractModel $campaign */
        $id = $campaign->getId();

        try {
            unset($this->instances[$id]);
            $this->resource->delete($campaign);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to remove Campaign %1', $id)
            );
        }

        return true;
    }

    /**
     * @param int $campaignId
     * @return bool true on success
     * @throws LocalizedException
     */
    public function deleteById($campaignId): bool
    {
        $campaign = $this->getById($campaignId);

        return $this->delete($campaign);
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection $collection
     * @return CampaignRepository
     */
    protected function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $collection): CampaignRepository
    {
        $fields     = [];
        $conditions = [];

        foreach ($filterGroup->getFilters() as $filter) {
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
