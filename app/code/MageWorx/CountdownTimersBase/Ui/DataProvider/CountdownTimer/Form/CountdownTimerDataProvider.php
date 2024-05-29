<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\CountdownTimersBase\Ui\DataProvider\CountdownTimer\Form;

use Magento\Eav\Api\AttributeSetRepositoryInterface;
use Magento\Store\Model\Store;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use MageWorx\CountdownTimersBase\Api\Data\CountdownTimerInterface;
use MageWorx\CountdownTimersBase\Model\ResourceModel\CountdownTimer\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatusOptions;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;

class CountdownTimerDataProvider extends AbstractDataProvider
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
     * CountdownTimerDataProvider constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param PoolInterface $pool
     * @param ProductCollectionFactory $productCollectionFactory
     * @param ProductStatusOptions $productStatusOptions
     * @param AttributeSetRepositoryInterface $attributeSetRepository
     * @param ImageHelper $imageHelper
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        PoolInterface $pool,
        ProductCollectionFactory $productCollectionFactory,
        ProductStatusOptions $productStatusOptions,
        AttributeSetRepositoryInterface $attributeSetRepository,
        ImageHelper $imageHelper,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->collection               = $collectionFactory->create();
        $this->dataPersistor            = $dataPersistor;
        $this->pool                     = $pool;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productStatusOptions     = $productStatusOptions;
        $this->attributeSetRepository   = $attributeSetRepository;
        $this->imageHelper              = $imageHelper;
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

        $data = $this->dataPersistor->get('mageworx_countdowntimersbase_countdown_timer');

        if (!empty($data)) {
            /** @var \MageWorx\CountdownTimersBase\Model\CountdownTimer $countdownTimer */
            $countdownTimer = $this->collection->getNewEmptyItem();
            $countdownTimer->setData($data);
            $this->loadedData[$countdownTimer->getId()] = $countdownTimer->getData();
            $this->dataPersistor->clear('mageworx_countdowntimersbase_countdown_timer');
        }

        if (key($this->loadedData)) {
            $this->prepareData(key($this->loadedData));
        }

        return $this->loadedData;
    }

    /**
     * @param string $countdownTimerId
     * @throws NoSuchEntityException
     */
    protected function prepareData($countdownTimerId): void
    {
        $this->prepareProducts($countdownTimerId);
        $this->prepareDesign($countdownTimerId);
    }

    /**
     * @param string $timerId
     * @throws NoSuchEntityException
     */
    protected function prepareProducts($timerId): void
    {
        if (!empty($this->loadedData[$timerId][CountdownTimerInterface::PRODUCT_IDS])) {
            $productIds = $this->loadedData[$timerId][CountdownTimerInterface::PRODUCT_IDS];

            /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
            $collection = $this->productCollectionFactory->create();

            $collection->addIdFilter($productIds);
            $collection->setStoreId(Store::DEFAULT_STORE_ID);
            $collection->addAttributeToSelect('status');
            $collection->addAttributeToSelect('name');
            $collection->addAttributeToSelect('thumbnail');

            $productsData = [];

            foreach ($collection as $product) {
                $productsData[] = $this->fillProductData($product);
            }

            $this->loadedData[$timerId]['specific-products'][CountdownTimerInterface::PRODUCT_IDS] = $productsData;
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
     * @param string $countdownTimerId
     */
    protected function prepareDesign($countdownTimerId): void
    {
        if (!empty($this->loadedData[$countdownTimerId][CountdownTimerInterface::THEME])) {
            $theme = $this->loadedData[$countdownTimerId][CountdownTimerInterface::THEME];

            $this->loadedData[$countdownTimerId]['templates'][CountdownTimerInterface::THEME] = $theme;
        }

        if (!empty($this->loadedData[$countdownTimerId][CountdownTimerInterface::ACCENT])) {
            $accent = $this->loadedData[$countdownTimerId][CountdownTimerInterface::ACCENT];

            $this->loadedData[$countdownTimerId]['templates'][CountdownTimerInterface::ACCENT] = $accent;
        }

        if (!empty($theme) && !empty($accent)) {
            $this->loadedData[$countdownTimerId]['templates']['preview'] = $theme . '-' . $accent;
        }
    }
}
