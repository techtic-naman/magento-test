<?php
namespace Tryathome\Core\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Tryathome\Core\Model\TryItemFactory;
use Tryathome\Core\Model\TrialProductUserFactory;
use Tryathome\Core\Model\ResourceModel\TrialProductUser as TrialProductUserResource; // Add this line 

class SaveData extends Action
{
    protected $resultJsonFactory;
    protected $tryModelFactory;
    protected $trialProductUserFactory; // Add this line
    protected $trialProductUserResource; // Add this line

    protected $cartModel;
    protected $webkulMarketplaceProduct;
    
    public function __construct(
    Context $context,
        JsonFactory $resultJsonFactory,
        TryItemFactory $tryModelFactory,
        \Magento\Checkout\Model\Cart $cartModel,
        \Webkul\Marketplace\Model\ResourceModel\Product\CollectionFactory $webkulMarketplaceProduct,
        TrialProductUserFactory $trialProductUserFactory, // Add this line
        TrialProductUserResource $trialProductUserResource // Add this line
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->tryModelFactory = $tryModelFactory;
        $this->cartModel = $cartModel;
        $this->trialProductUserFactory = $trialProductUserFactory; // Add this line
        $this->trialProductUserResource = $trialProductUserResource; // Add this line
        $this->webkulMarketplaceProduct = $webkulMarketplaceProduct;
        parent::__construct($context);
    }

    public function execute()
    {
        // $config = include '../../../../../../app/etc/env.php';

        // // Extract the database connection details
        // $dbConfig = $config['db']['connection']['default'];
        // $host = $dbConfig['host'];
        // $dbname = $dbConfig['dbname'];
        // $username = $dbConfig['username'];
        // print_r($this->webkulMarketplaceProduct->create()->getData());exit;
        // $password = $dbConfig['password'];
        $result = $this->resultJsonFactory->create();
        $data = $this->getRequest()->getPostValue();
        $marketplaceProduct = $this->webkulMarketplaceProduct->create();

        $data['user_id'] = 1;
        // $data['seller_id'] = 1;
        // $data['product_ids'] = json_encode(['product_id' => 267]);

        // return $result->setData(['success' => $data]);
        // exit;

        if (!empty($data)) {
            $tryModel = $this->tryModelFactory->create();
            $tryModel->setData($data);
            $tryModel->save();
            $tryId = $tryModel->getId();

            //Get the cart object
            //  $cart = $this->_objectManager->get('\Magento\Checkout\Model\Cart');

            //Get the cart items
            $items = $this->cartModel->getQuote()->getAllVisibleItems();
            
            $itemsWithZeroPrice = array_filter($items, function($item) {
                return $item->getPrice() == 0;
            });

            foreach ($itemsWithZeroPrice as $item) {
                $trialProductUser = $this->trialProductUserFactory->create(); 
                $sellerId = $marketplaceProduct->addFieldToFilter('mageproduct_id', $item->getProductId())->getFirstItem()->getSellerId();

                $trialProductUser->setData([
                    'try_id' => $tryId,
                    'product_id' => $item->getProductId(),
                    'seller_id' => $sellerId
                ]);
                $trialProductUser->save();
            }

            return $result->setData(['success' => true,'try_id' => $tryId]);
        }

        return $result->setData(['success' => false]);
    }
    }