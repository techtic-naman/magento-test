<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types = 1);

namespace MageWorx\ReviewAIBase\Controller\Adminhtml\ReviewSummary;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use MageWorx\ReviewAIBase\Api\Data\ReviewSummaryInterface;
use MageWorx\ReviewAIBase\Api\ReviewSummaryGeneratorInterface;
use MageWorx\ReviewAIBase\Model\Config\Source\Status;
use MageWorx\ReviewAIBase\Model\ResourceModel\ReviewSummary as ReviewSummaryResource;
use MageWorx\ReviewAIBase\Model\ResourceModel\ReviewSummary\Grid\CollectionFactory as GridCollectionFactory;

class AddToQueueMassAction extends Action
{
    protected Filter $filter;

    protected GridCollectionFactory           $gridCollectionFactory;
    protected ReviewSummaryGeneratorInterface $reviewSummaryGenerator;
    protected ReviewSummaryResource           $reviewSummaryResource;

    public function __construct(
        Context                         $context,
        Filter                          $filter,
        GridCollectionFactory           $gridCollectionFactory,
        ReviewSummaryResource           $reviewSummaryResource,
        ReviewSummaryGeneratorInterface $reviewSummaryGenerator
    ) {
        $this->filter                 = $filter;
        $this->gridCollectionFactory  = $gridCollectionFactory;
        $this->reviewSummaryResource  = $reviewSummaryResource;
        $this->reviewSummaryGenerator = $reviewSummaryGenerator;

        parent::__construct($context);
    }

    public function execute()
    {
        $storeId = (int)$this->getRequest()->getParam('store_id');

        $collection = $this->filter->getCollection($this->gridCollectionFactory->create());
        $collection->addStoreFilterToJoin($storeId);

        $productIds = $collection->getAllIds();
        $productIds = array_unique($productIds);

        $itemsUpdated = 0;

        $itemsUpdated += $this->reviewSummaryResource->massTriggerUpdate($productIds, $storeId);

        $existingProductIds = $collection->getColumnValues('product_id');
        $missingProductIds  = array_diff($productIds, $existingProductIds);

        $itemsUpdated += $this->reviewSummaryResource->massInsertOnDuplicateKeyIgnore(
            $missingProductIds,
            $storeId,
            [ReviewSummaryInterface::UPDATE_REQUIRED => false, ReviewSummaryInterface::STATUS => Status::STATUS_IN_QUEUE]
        );

        $queuedItems = 0;
        foreach ($productIds as $productId) {
            $queueItems = $this->reviewSummaryGenerator->addToQueue((int)$productId, $storeId);
            $queuedItems += count($queueItems);
        }

        if ($queuedItems) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) were added to queue. %2 items were updated.', $queuedItems, $itemsUpdated));
        } else {
            $this->messageManager->addErrorMessage(__('No records were added to queue.'));
        }

        return $this->_redirect($this->_redirect->getRefererUrl());
    }
}
