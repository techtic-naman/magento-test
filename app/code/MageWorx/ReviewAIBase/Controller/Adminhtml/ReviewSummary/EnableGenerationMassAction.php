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
use MageWorx\ReviewAIBase\Api\ReviewSummarySaverInterface;
use MageWorx\ReviewAIBase\Model\Config\Source\Status;
use MageWorx\ReviewAIBase\Model\ResourceModel\ReviewSummary\CollectionFactory as ReviewSummaryCollectionFactory;
use MageWorx\ReviewAIBase\Model\ResourceModel\ReviewSummary\Grid\CollectionFactory as GridCollectionFactory;

class EnableGenerationMassAction extends Action
{
    protected Filter $filter;

    protected GridCollectionFactory           $gridCollectionFactory;
    protected ReviewSummaryCollectionFactory  $reviewSummaryCollectionFactory;
    protected ReviewSummarySaverInterface     $reviewSummarySaver;
    protected ReviewSummaryGeneratorInterface $reviewSummaryGenerator;

    public function __construct(
        Context                         $context,
        Filter                          $filter,
        GridCollectionFactory           $gridCollectionFactory,
        ReviewSummaryCollectionFactory  $reviewSummaryCollectionFactory,
        ReviewSummarySaverInterface     $reviewSummarySaver,
        ReviewSummaryGeneratorInterface $reviewSummaryGenerator
    ) {
        $this->filter                         = $filter;
        $this->gridCollectionFactory          = $gridCollectionFactory;
        $this->reviewSummaryCollectionFactory = $reviewSummaryCollectionFactory;
        $this->reviewSummarySaver             = $reviewSummarySaver;
        $this->reviewSummaryGenerator         = $reviewSummaryGenerator;
        parent::__construct($context);
    }

    public function execute()
    {
        $storeId = (int)$this->getRequest()->getParam('store_id');

        $collection = $this->filter->getCollection($this->gridCollectionFactory->create());
        $collection->addStoreFilterToJoin($storeId);

        $reviewSummaryCollection = $this->reviewSummaryCollectionFactory->create();

        $productIds = $collection->getAllIds();
        $productIds = array_unique($productIds);

        $itemsUpdated = 0;

        $reviewSummaryResource = $reviewSummaryCollection->getResource();
        $itemsUpdated          += $reviewSummaryResource->massEnable($productIds, $storeId);

        $reviewSummaryCollection->addFieldToFilter('product_id', ['in' => $productIds]);
        $existingProductIds = $collection->getColumnValues('product_id');
        $missingProductIds  = array_diff($productIds, $existingProductIds);

        $itemsUpdated += $reviewSummaryResource->massInsertOnDuplicateKeyIgnore(
            $missingProductIds,
            $storeId,
            [ReviewSummaryInterface::IS_ENABLED => true, ReviewSummaryInterface::STATUS => Status::STATUS_PENDING]
        );

        foreach ($productIds as $productId) {
            $this->reviewSummaryGenerator->addToQueue(
                (int)$productId,
                $storeId
            );
        }

        if ($itemsUpdated) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) were updated.', $itemsUpdated));
        } else {
            $this->messageManager->addErrorMessage(__('No records were updated.'));
        }

        return $this->_redirect($this->_redirect->getRefererUrl());
    }
}
