<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\RewardPoints\Model;

use Magento\Customer\Api\Data\GroupInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use MageWorx\RewardPoints\Api\Data\RewardPromiseInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\Catalog\Model\Product\Visibility as ProductVisibility;
use Magento\Framework\Event\ManagerInterface as EventManager;

class RewardPromiseManager implements \MageWorx\RewardPoints\Api\RewardPromiseManagerInterface
{
    /**
     * @var \MageWorx\RewardPoints\Api\Data\RewardPromiseInterfaceFactory
     */
    protected $rewardPromiseInterfaceFactory;

    /**
     * @var \MageWorx\RewardPoints\Model\RuleActionValidator
     */
    protected $ruleActionValidator;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ProductStatus
     */
    protected $productStatus;

    /**
     * @var ProductVisibility
     */
    protected $productVisibility;

    /**
     * @var EventManager
     */
    protected $eventManager;

    /**
     * @var int
     */
    protected $pageSize = 100;

    /**
     * RewardPromiseManager constructor.
     *
     * @param \MageWorx\RewardPoints\Api\Data\RewardPromiseInterfaceFactory $rewardPromiseInterfaceFactory
     * @param RuleActionValidator $ruleActionValidator
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param ProductStatus $productStatus
     * @param ProductVisibility $productVisibility
     * @param EventManager $eventManager
     */
    public function __construct(
        \MageWorx\RewardPoints\Api\Data\RewardPromiseInterfaceFactory $rewardPromiseInterfaceFactory,
        \MageWorx\RewardPoints\Model\RuleActionValidator $ruleActionValidator,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        ProductStatus $productStatus,
        ProductVisibility $productVisibility,
        EventManager $eventManager
    ) {
        $this->rewardPromiseInterfaceFactory = $rewardPromiseInterfaceFactory;
        $this->ruleActionValidator           = $ruleActionValidator;
        $this->productCollectionFactory      = $productCollectionFactory;
        $this->customerRepository            = $customerRepository;
        $this->storeManager                  = $storeManager;
        $this->productStatus                 = $productStatus;
        $this->productVisibility             = $productVisibility;
        $this->eventManager                  = $eventManager;
    }

    /**
     * @param int $customerId
     * @param array $productIds
     * @return array
     */
    public function getByProductIds(int $customerId, array $productIds): array
    {
        try {
            $customer = $this->customerRepository->getById($customerId);
        } catch (NoSuchEntityException $e) {
            return [];
        }

        return $this->findByProductIds($productIds, $customer);
    }

    /**
     * @param array $productIds
     * @param \Magento\Customer\Api\Data\CustomerInterface|null $customer
     * @retun array
     */
    public function findByProductIds(array $productIds, $customer = null): array
    {
        $customerGroupId   = $customer ? (int)$customer->getGroupId() : GroupInterface::NOT_LOGGED_IN_ID;
        $productCollection = $this->getProductCollection($customerGroupId, $productIds);

        $result = [];

        foreach ($productIds as $id) {

            $product = $productCollection->getItemByColumnValue('entity_id', $id);

            if ($product === null) {
                $data = [
                    RewardPromiseInterface::AMOUNT_KEY     => 0,
                    RewardPromiseInterface::PRODUCT_ID_KEY => $id
                ];
            } else {
                $data = [
                    RewardPromiseInterface::AMOUNT_KEY     => $this->findProductReward($product),
                    RewardPromiseInterface::PRODUCT_ID_KEY => $id
                ];
            }

            /** @var RewardPromiseInterface $rewardPromise */
            $rewardPromise = $this->rewardPromiseInterfaceFactory->create(['data' => $data]);

            $result[] = $rewardPromise;
        }

        return $result;
    }

    /**
     * @param int $customerGroupId
     * @param array $productIds
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getProductCollection(
        int $customerGroupId,
        array $productIds
    ): \Magento\Catalog\Model\ResourceModel\Product\Collection {

        $collection = $this->productCollectionFactory->create();
        $collection
            ->addAttributeToSelect('*')
            ->addIdFilter($productIds)
            ->addPriceData($customerGroupId, $this->storeManager->getWebsite()->getId())
            ->addStoreFilter($this->storeManager->getStore())
            ->setStore($this->storeManager->getStore())
            ->setVisibility($this->productVisibility->getVisibleInCatalogIds())
            ->addAttributeToFilter(
                'status',
                [
                    'in' => $this->productStatus->getVisibleStatusIds()
                ]
            )->setPageSize($this->pageSize);

        $this->eventManager->dispatch(
            'mageworx_rewardpoints_promise_product_collection_load_before',
            ['collection' => $collection, 'productIds' => $productIds]
        );

        foreach ($collection as $product) {
            $product->setCustomerGroupId($customerGroupId);
        }

        return $collection;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return float
     */
    protected function findProductReward(\Magento\Catalog\Model\Product $product): float
    {
        return $this->ruleActionValidator->getPointsByProduct($product);
    }
}
