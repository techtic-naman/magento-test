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
namespace Webkul\Marketplace\Observer;

use Magento\Framework\Event\ObserverInterface;
use Webkul\Marketplace\Model\ResourceModel\Product\CollectionFactory;
use Webkul\Marketplace\Model\ProductFactory as MpProductFactory;
use Webkul\Marketplace\Helper\Data as MpHelper;
use Magento\Framework\Event\Manager;

/**
 * Webkul Marketplace CatalogProductSaveAfterObserver Observer.
 */
class CatalogProductSaveAfterObserver implements ObserverInterface
{
    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var MpProductFactory
     */
    protected $mpProductFactory;

    /**
     * @var MpHelper
     */
    protected $mpHelper;

    /**
     * @var Manager
     */
    private $eventManager;
    /**
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param CollectionFactory                           $collectionFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param MpProductFactory                            $mpProductFactory
     * @param MpHelper                                    $mpHelper
     * @param EventManager                                $eventManager
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        CollectionFactory $collectionFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        MpProductFactory $mpProductFactory,
        MpHelper $mpHelper,
        Manager $eventManager
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_date = $date;
        $this->messageManager = $messageManager;
        $this->mpProductFactory = $mpProductFactory;
        $this->mpHelper = $mpHelper;
        $this->eventManager = $eventManager;
    }

    /**
     * Product save after event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $product = $observer->getProduct();
            $assginSellerData = $product->getAssignSeller();
            $productId = $observer->getProduct()->getId();
            $status = $observer->getProduct()->getStatus();
            $productCollection = $this->mpProductFactory->create()
                                ->getCollection()
                                ->addFieldToFilter(
                                    'mageproduct_id',
                                    $productId
                                );
            $sellerId = isset($assginSellerData['seller_id'])?$assginSellerData['seller_id']:"";
            if (isset($sellerId) && $sellerId != "" && !$product->getSellerId()) {
                $product->setSellerId($assginSellerData['seller_id']);
                $product->save();
            }
            if ($productCollection->getSize()) {
                foreach ($productCollection as $product) {
                    if ($status != $product->getStatus()) {
                        $product->setStatus($status)->save();
                    }
                }
            } elseif (is_array($assginSellerData) &&
            isset($assginSellerData['seller_id']) &&
            $assginSellerData['seller_id'] != ''
            ) {
                $sellerId = $assginSellerData['seller_id'];
                $mpProductModel = $this->mpProductFactory->create();
                $mpProductModel->setMageproductId($productId);
                $mpProductModel->setSellerId($sellerId);
                $mpProductModel->setStatus($product->getStatus());
                $mpProductModel->setAdminassign(1);
                $isApproved = 1;
                if ($product->getStatus() == 2 && $this->mpHelper->getIsProductApproval()) {
                    $isApproved = 0;
                }
                $mpProductModel->setIsApproved($isApproved);
                $mpProductModel->setCreatedAt($this->_date->gmtDate());
                $mpProductModel->save();
                $this->eventManager->dispatch(
                    'assign_product_to_seller_after',
                    ['productId' => $productId, 'sellerId' => $sellerId]
                );
            }
        } catch (\Exception $e) {
            $this->mpHelper->logDataInLogger(
                "Observer_CatalogProductSaveAfterObserver execute : ".$e->getMessage()
            );
            $this->messageManager->addError($e->getMessage());
        }
    }
}
