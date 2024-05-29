<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\StockStatus\Controller\Stock;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

/**
 * Class Update
 */
class Update extends Action
{
    /**
     * @var \MageWorx\StockStatus\Model\ProductStock
     */
    protected $productStock;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Update constructor.
     *
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param ProductRepositoryInterface $productRepository
     * @param \MageWorx\StockStatus\Model\ProductStock $productStock
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param Context $context
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository,
        \MageWorx\StockStatus\Model\ProductStock $productStock,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        Context $context
    ) {
        $this->storeManager      = $storeManager;
        $this->productRepository = $productRepository;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->productStock      = $productStock;
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

        $productId      = $this->getRequest()->getParam('product');
        $superAttribute = $this->getRequest()->getParam('super_attribute') ?? [];
        $product        = $this->productRepository->getById(
            $productId,
            false,
            $this->storeManager->getStore()->getId()
        );

        $text   = $this->productStock->getProductStockText($product, $superAttribute);
        $result = $this->resultJsonFactory->create();

        return $result->setData($text);
    }
}