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
namespace Webkul\Marketplace\Ui\Component\Listing\Columns\Frontend;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Webkul\Marketplace\Model\ResourceModel\Saleslist\CollectionFactory;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Webkul\Marketplace\Helper\Data as HelperData;
use Magento\Framework\UrlInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Quantity sold class.
 */
class ProductStatus extends Column
{

    /**
     * @var HelperData
     */
    public $helperData;
    
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepositoryInterface;

    /**
     * Constructor.
     *
     * @param ContextInterface              $context
     * @param UiComponentFactory            $uiComponentFactory
     * @param CollectionFactory             $collectionFactory
     * @param HelperData                    $helperData
     * @param UrlInterface                  $urlBuilder
     * @param ProductRepositoryInterface    $productRepositoryInterface
     * @param array                         $components
     * @param array                         $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CollectionFactory $collectionFactory,
        HelperData $helperData,
        UrlInterface $urlBuilder,
        ProductRepositoryInterface $productRepositoryInterface,
        array $components = [],
        array $data = []
    ) {
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->helperData = $helperData;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source.
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            $sellerId = $this->helperData->getCustomerId();
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['entity_id'])) {
                    $storeId = $this->helperData->getCurrentStoreId();
                    $product = $this->productRepositoryInterface
                    ->getById(
                        $item['mageproduct_id'],
                        false,
                        $storeId
                    );
                    $item[$fieldName] = $product->getStatus();
                }
            }
        }

        return $dataSource;
    }
}
