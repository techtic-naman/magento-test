<?php

namespace Tryathome\Core\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;
    protected $cacheManager;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\App\Cache\Manager $cacheManager
        )
    {
        $this->_pageFactory = $pageFactory;
        $this->cacheManager = $cacheManager;
        return parent::__construct($context);
    }

    public function execute()
    {
        $this->cacheManager->flush($this->cacheManager->getAvailableTypes());
        // Get the cart object
    //    $cart = $this->_objectManager->get('\Magento\Checkout\Model\Cart');

        // Get the cart items
        // $items = $cart->getQuote()->getAllVisibleItems();

        // print_r($items->debug());
        // die("dead");

        // Loop through the items and print each one
        // foreach ($items as $item) {
        //     print_r($item->debug());
        // }

        // die("dead");

        // $itemsWithZeroPrice = array_filter($items, function($item) {
        //     return $item->getPrice() == 0;
        // });

        // foreach ($itemsWithZeroPrice as $item) {
        //     // echo $item->getProductId();
        //    // Step 2: Fetch the seller_id based on mageproduct_id
        //     $pdo = new \PDO("mysql:host=localhost;dbname=magento", "root", "");
        //     $stmt = $pdo->prepare("SELECT seller_id FROM marketplace_product WHERE mageproduct_id = :mageproduct_id");
        //     $stmt->execute([':mageproduct_id' => $item->getProductId()]);
        //     $sellerId = $stmt->fetchColumn();
        //     if ($sellerId !== false) {
        //         $item->setSellerId($sellerId);
        //         // print_r($item->debug());
        //     }

        // }

        // die("dead");

        // Prepare the data to be passed to the view
        $data = [
            // 'cartItems' => $itemsWithZeroPrice,
            // Add any other data you want to pass to the view
        ];

        // Create a result page
        $resultPage = $this->_pageFactory->create();

        $block = $resultPage->getLayout()->getBlock('tryathome.core.index');

        // Set the data to the result page
        $resultPage->getConfig()->getTitle()->set(__('Trial Details'));
        $resultPage->getLayout()->getBlock('tryathome.core.index')->setData('cartData', $data);

        return $resultPage;
    }
    
}