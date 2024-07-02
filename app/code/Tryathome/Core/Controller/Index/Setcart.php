<?php

namespace Tryathome\Core\Controller\Index;

class Setcart extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;
    protected $cacheManager;
    protected $checkoutSession;
    protected $resultJsonFactory;
    protected $imageHelper;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\App\Cache\Manager $cacheManager,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Catalog\Helper\Image $imageHelper // Injecting the Image helper
    ) {
        $this->_pageFactory = $pageFactory;
        $this->cacheManager = $cacheManager;
        $this->checkoutSession = $checkoutSession;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->imageHelper = $imageHelper; // Initializing the Image helper
        parent::__construct($context);
    }

    public function execute()
    {
        $cart = $this->checkoutSession->getQuote();
        $items = $cart->getAllVisibleItems();
        $itemsData = [];

        $itemsWithZeroPrice = array_filter($items, function($item) {
            return $item->getPrice() == 0;
        });

        foreach ($itemsWithZeroPrice as $item) {
            $product = $item->getProduct();
            $imageUrl = $this->imageHelper->init($product, 'product_thumbnail_image')->getUrl();

            $itemsData[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'qty' => $item->getQty(),
                'price' => $product->getPrice(),
                'image_url' => $imageUrl // Adding the image URL
            ];
        }

        $result = $this->resultJsonFactory->create();
        return $result->setData(['items' => $itemsData]);
    }
}