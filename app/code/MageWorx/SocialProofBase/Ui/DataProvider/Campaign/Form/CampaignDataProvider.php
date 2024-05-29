<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\SocialProofBase\Ui\DataProvider\Campaign\Form;

use Magento\Eav\Api\AttributeSetRepositoryInterface;
use Magento\Store\Model\Store;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use MageWorx\SocialProofBase\Api\Data\CampaignInterface;
use MageWorx\SocialProofBase\Model\ResourceModel\Campaign\CollectionFactory as CampaignCollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatusOptions;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as CmsPageCollectionFactory;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;

class CampaignDataProvider extends AbstractDataProvider
{
    /**
     * Loaded data cache
     *
     * @var array
     */
    protected $loadedData = [];

    /**
     * Data persistor
     *
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var PoolInterface
     */
    private $pool;

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var ProductStatusOptions
     */
    protected $productStatusOptions;

    /**
     * @var AttributeSetRepositoryInterface
     */
    protected $attributeSetRepository;

    /**
     * @var ImageHelper
     */
    protected $imageHelper;

    /**
     * @var CmsPageCollectionFactory
     */
    protected $cmsPageCollectionFactory;

    /**
     * CampaignDataProvider constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CampaignCollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param PoolInterface $pool
     * @param ProductCollectionFactory $productCollectionFactory
     * @param ProductStatusOptions $productStatusOptions
     * @param AttributeSetRepositoryInterface $attributeSetRepository
     * @param ImageHelper $imageHelper
     * @param CmsPageCollectionFactory $cmsPageCollectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CampaignCollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        PoolInterface $pool,
        ProductCollectionFactory $productCollectionFactory,
        ProductStatusOptions $productStatusOptions,
        AttributeSetRepositoryInterface $attributeSetRepository,
        ImageHelper $imageHelper,
        CmsPageCollectionFactory $cmsPageCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->dataPersistor            = $dataPersistor;
        $this->collection               = $collectionFactory->create();
        $this->pool                     = $pool;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productStatusOptions     = $productStatusOptions;
        $this->attributeSetRepository   = $attributeSetRepository;
        $this->imageHelper              = $imageHelper;
        $this->cmsPageCollectionFactory = $cmsPageCollectionFactory;
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getMeta(): array
    {
        $meta      = parent::getMeta();
        $modifiers = $this->pool->getModifiersInstances();

        /** @var ModifierInterface $modifier */
        foreach ($modifiers as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }

        return $meta;
    }

    /**
     * Get data
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function getData(): array
    {
        if (!empty($this->loadedData)) {
            return $this->loadedData;
        }

        $campaigns = $this->collection->getItems();

        /** @var \MageWorx\SocialProofBase\Model\Campaign $campaign */
        foreach ($campaigns as $campaign) {
            $this->loadedData[$campaign->getId()] = $campaign->getData();
        }

        $data = $this->dataPersistor->get('mageworx_socialproofbase_campaign');

        if (!empty($data)) {
            $campaign = $this->collection->getNewEmptyItem();
            $campaign->setData($data);
            $this->loadedData[$campaign->getId()] = $campaign->getData();
            $this->dataPersistor->clear('mageworx_socialproofbase_campaign');
        }

        if (key($this->loadedData)) {
            $this->prepareData(key($this->loadedData));
        }

        return $this->loadedData;
    }

    /**
     * @param string $campaignId
     * @throws NoSuchEntityException
     */
    protected function prepareData($campaignId): void
    {
        $this->prepareProducts($campaignId);
        $this->prepareCmsPages($campaignId);
        $this->preparePosition($campaignId);
        $this->prepareContent($campaignId);
    }

    /**
     * @param string $campaignId
     * @throws NoSuchEntityException
     */
    protected function prepareProducts($campaignId): void
    {
        if (!empty($this->loadedData[$campaignId][CampaignInterface::PRODUCT_IDS])) {
            $productIds = $this->loadedData[$campaignId][CampaignInterface::PRODUCT_IDS];

            /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
            $collection = $this->productCollectionFactory->create();

            $collection->addIdFilter($productIds);
            $collection->setStoreId(Store::DEFAULT_STORE_ID);
            $collection->addAttributeToSelect('name');
            $collection->addAttributeToSelect('status');
            $collection->addAttributeToSelect('thumbnail');

            $productsData = [];

            foreach ($collection as $product) {
                $productsData[] = $this->fillProductData($product);
            }

            $this->loadedData[$campaignId]['specific-products'][CampaignInterface::PRODUCT_IDS] = $productsData;
        }
    }

    /**
     * @param ProductInterface $product
     * @return array
     * @throws NoSuchEntityException
     */
    protected function fillProductData($product): array
    {
        return [
            'id'            => $product->getId(),
            'name'          => $product->getName(),
            'status'        => $this->productStatusOptions->getOptionText($product->getStatus()),
            'attribute_set' => $this->attributeSetRepository->get($product->getAttributeSetId())->getAttributeSetName(),
            'sku'           => $product->getSku(),
            'price'         => $product->getPrice(),
            'thumbnail'     => $this->imageHelper->init($product, 'product_listing_thumbnail')->getUrl()
        ];
    }

    /**
     * @param string $campaignId
     */
    protected function prepareCmsPages($campaignId): void
    {
        if (!empty($this->loadedData[$campaignId][CampaignInterface::CMS_PAGE_IDS])) {
            $pageIds = $this->loadedData[$campaignId][CampaignInterface::CMS_PAGE_IDS];

            /** @var \Magento\Cms\Model\ResourceModel\Page\Collection $collection */
            $collection = $this->cmsPageCollectionFactory->create();

            $collection->addFieldToSelect('page_id');
            $collection->addFieldToSelect('title');
            $collection->addFieldToSelect('identifier');
            $collection->addFieldToSelect('is_active');
            $collection->addFieldToSelect('creation_time');
            $collection->addFieldToSelect('update_time');
            $collection->addFieldToFilter('page_id', ['in' => $pageIds]);

            $pagesData = [];

            foreach ($collection->getData() as $page) {
                $pagesData[] = $this->fillCmsPageData($page);
            }

            $this->loadedData[$campaignId]['specific-cms-pages'][CampaignInterface::CMS_PAGE_IDS] = $pagesData;
        }
    }

    /**
     * @param array $page
     * @return array
     */
    protected function fillCmsPageData($page): array
    {
        return [
            'id'            => $page['page_id'],
            'title'         => $page['title'],
            'identifier'    => $page['identifier'],
            'is_active'     => $page['is_active'],
            'creation_time' => $page['creation_time'],
            'update_time'   => $page['update_time']
        ];
    }

    /**
     * @param string $campaignId
     */
    protected function preparePosition($campaignId): void
    {
        if (!empty($this->loadedData[$campaignId][CampaignInterface::POSITION])) {
            $position = $this->loadedData[$campaignId][CampaignInterface::POSITION];

            $this->loadedData[$campaignId]['position-wrapper'][CampaignInterface::POSITION] = $position;
        }
    }

    /**
     * @param string $campaignId
     */
    protected function prepareContent($campaignId): void
    {
        if (!empty($this->loadedData[$campaignId][CampaignInterface::CONTENT])) {
            $content = $this->loadedData[$campaignId][CampaignInterface::CONTENT];

            $this->loadedData[$campaignId]['templates'][CampaignInterface::CONTENT] = $content;
        }
    }
}
