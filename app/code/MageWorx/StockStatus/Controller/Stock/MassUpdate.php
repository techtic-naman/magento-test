<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\StockStatus\Controller\Stock;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

/**
 * Class MassUpdate
 */
class MassUpdate extends Action
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \MageWorx\StockStatus\Model\ProductStock
     */
    protected $productStock;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * MassUpdate constructor.
     *
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param ProductRepositoryInterface $productRepository
     * @param \MageWorx\StockStatus\Model\ProductStock $productStock
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param Context $context
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository,
        \MageWorx\StockStatus\Model\ProductStock $productStock,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        Context $context
    ) {
        $this->searchCriteriaBuilder   = $searchCriteriaBuilder;
        $this->filterBuilder           = $filterBuilder;
        $this->storeManager            = $storeManager;
        $this->productRepository       = $productRepository;
        $this->resultJsonFactory       = $resultJsonFactory;
        $this->productStock            = $productStock;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        if (!$this->getRequest()->isAjax()) {
            return;
        }

        $productIds = $this->getRequest()->getParam('productIds');
        if (empty($productIds)) {
            return;
        }

        $filter = $this->filterBuilder
            ->setField('entity_id')
            ->setConditionType('in')
            ->setValue($productIds)
            ->create();

        $storeFilter = $this->filterBuilder
            ->setField('store_id')
            ->setConditionType('eq')
            ->setValue($this->storeManager->getStore()->getId())
            ->create();

        $this->searchCriteriaBuilder->addFilters([$filter, $storeFilter]);
        $searchCriteria    = $this->searchCriteriaBuilder->create();
        $products = $this->productRepository->getList($searchCriteria)->getItems();

        $data = [];
        foreach ($products as $product) {
            $block = $this->_view->getLayout()->createBlock('MageWorx\StockStatus\Block\StockStatus');
            $block->setProduct($product);

            $data[$product->getId()] = $block->toHtml();
        }

        $result = $this->resultJsonFactory->create();

        return $result->setData($data);
    }
}