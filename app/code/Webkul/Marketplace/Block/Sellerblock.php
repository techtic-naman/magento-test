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

namespace Webkul\Marketplace\Block;

/*
 * Webkul Marketplace Sellerblock Block
 */
use Magento\Customer\Model\Customer;
use Magento\Catalog\Model\Product;
use Webkul\Marketplace\Model\ResourceModel\ProductFlagReason\CollectionFactory;

class Sellerblock extends \Magento\Framework\View\Element\Template
{
    public const FLAG_REASON_ENABLE = 1;
    public const FLAG_REASON_DISABLE = 0;

    /**
     * @var Product
     */
    protected $_product = null;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $Customer;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $Session;

    /**
     * @var \Webkul\Marketplace\Helper\Data
     */
    protected $mpHelper;

    /**
     * @var \Webkul\Marketplace\Model\ResourceModel\ProductFlagReason\Collection
     */
    protected $reasonCollection;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param Customer $customer
     * @param \Magento\Customer\Model\Session $session
     * @param \Webkul\Marketplace\Helper\Data $mpHelper
     * @param array $data
     * @param CollectionFactory|null $reasonCollection
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        Customer $customer,
        \Magento\Customer\Model\Session $session,
        \Webkul\Marketplace\Helper\Data $mpHelper,
        array $data = [],
        CollectionFactory $reasonCollection = null
    ) {
        $this->Customer = $customer;
        $this->Session = $session;
        $this->_coreRegistry = $registry;
        $this->mpHelper = $mpHelper;
        $this->reasonCollection = $reasonCollection ?: \Magento\Framework\App\ObjectManager::getInstance()
                                  ->create(CollectionFactory::class);
        parent::__construct($context, $data);
    }

    /**
     * Get product information
     *
     * @return Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            $this->_product = $this->_coreRegistry->registry('product');
        }

        return $this->_product;
    }

    /**
     * GetProductFlagReasons is used to get the product Flag Reasons
     *
     * @return \Webkul\Marketplace\Model\ResourceModel\ProductFlagReason\Collection
     */
    public function getProductFlagReasons()
    {
        $reasonCollection = $this->reasonCollection->create()
                          ->addFieldToFilter('status', self::FLAG_REASON_ENABLE)
                          ->setPageSize(5);
        return $reasonCollection;
    }
}
