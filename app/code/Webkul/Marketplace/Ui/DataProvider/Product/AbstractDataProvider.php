<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Marketplace\Ui\DataProvider\Product;

use Magento\Catalog\Ui\DataProvider\Product\Related\AbstractDataProvider as CatalogAbstractDataProvider;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Api\ProductLinkRepositoryInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Webkul\Marketplace\Model\ProductFactory;
use Webkul\Marketplace\Helper\Data as MpHelper;

/**
 * Class AbstractDataProvider
 */
abstract class AbstractDataProvider extends CatalogAbstractDataProvider
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * @var ProductLinkRepositoryInterface
     */
    protected $productLinkRepository;

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var ProductFactory
     */
    protected $mpProductModel;
    /**
     * @var MpHelper
     */
    protected $mpHelper;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param ProductRepositoryInterface $productRepository
     * @param StoreRepositoryInterface $storeRepository
     * @param ProductLinkRepositoryInterface $productLinkRepository
     * @param \Magento\Customer\Model\Session $customerSession
     * @param ProductFactory $mpProductModel
     * @param MpHelper $mpHelper
     * @param array $addFieldStrategies
     * @param array $addFilterStrategies
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        ProductRepositoryInterface $productRepository,
        StoreRepositoryInterface $storeRepository,
        ProductLinkRepositoryInterface $productLinkRepository,
        \Magento\Customer\Model\Session $customerSession,
        ProductFactory $mpProductModel,
        MpHelper $mpHelper,
        $addFieldStrategies,
        $addFilterStrategies,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $collectionFactory,
            $request,
            $productRepository,
            $storeRepository,
            $productLinkRepository,
            $addFieldStrategies,
            $addFilterStrategies,
            $meta,
            $data
        );

        $this->request = $request;
        $this->productRepository = $productRepository;
        $this->storeRepository = $storeRepository;
        $this->productLinkRepository = $productLinkRepository;
        $this->_customerSession = $customerSession;
        $this->mpProductModel = $mpProductModel;
        $this->mpHelper = $mpHelper;
    }

    /**
     * Get collection
     */
    public function getCollection()
    {
        if (!($sellerId = $this->_customerSession->getCustomerId())) {
            $sellerId = 0;
        }
        $marketplaceProduct = $this->mpProductModel->create()
        ->getCollection()
        ->addFieldToFilter('seller_id', $sellerId);
        $allIds = $marketplaceProduct->getAllIds();
        /** @var Collection $collection */
        $collection = parent::getCollection();
        $collection->addAttributeToSelect('status');
        $collection->addFieldToFilter('entity_id', ['in' => $allIds]);

        if ($this->mpHelper->getCurrentStoreId()) {
            $collection->setStore($this->mpHelper->getCurrentStoreId());
        }

        if (!$this->getProduct()) {
            return $collection;
        }

        $collection->addAttributeToFilter(
            $collection->getIdFieldName(),
            ['nin' => [$this->getProduct()->getId()]]
        );

        return $this->addCollectionFilters($collection);
    }
}
