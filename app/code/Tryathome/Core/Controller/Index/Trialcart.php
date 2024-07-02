<?php

namespace Tryathome\Core\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Checkout\Model\Cart;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ConfigurableProduct\Model\OptionRepository;

class Trialcart extends Action
{
    protected $cart;
    protected $resultJsonFactory;
    protected $optionRepository; // Add this line
    protected $productRepository; // Add this line for the product repository

    public function __construct(
        Context $context,
        Cart $cart,
        JsonFactory $resultJsonFactory,
        OptionRepository $optionRepository, // Inject OptionRepository here
        ProductRepositoryInterface $productRepository // Inject ProductRepositoryInterface here
    ) {
        $this->cart = $cart;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->optionRepository = $optionRepository; // Initialize it here
        $this->productRepository = $productRepository; // Initialize it here
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $itemId = (int)$this->getRequest()->getParam('item_id');
        $selected_configurable_option = (int)$this->getRequest()->getParam('selected_configurable_option');
        if ($itemId) {
            try {

                $params = [
                    "product"=> 10,
                    "selected_configurable_option"=> 26,
                    "item"=> 10,
                    "super_attribute"=>[
                        93 => 4,
                        162 => 6,
                        171 => 30,
                        170 => 27,
                        172 => 34,
                        169 => 24   
                    ],
                    "qty"=> 1
                ];

                $product = $this->productRepository->getById($itemId);

                $item = $this->cart->addProduct($product, $params);


                print_r($item->debug());
                exit;

                if (isset($selected_configurable_option)) {
                    // Load the configurable product option
                    $option = $this->optionRepository->get($product->getSku(),$selected_configurable_option);
                    
                    // Prepare option data for adding to cart
                    $params = new \Magento\Framework\DataObject([
                        'product' => $product->getData(),
                        'selected_configurable_option' => $option->getData(),
                        // Add any other necessary parameters here
                    ]);
                } else {
                    // Prepare basic data for adding to cart without configurable options
                    $params = new \Magento\Framework\DataObject([
                        'product' => $product->getData(),
                        // Add any other necessary parameters here
                    ]);
                    
                }
                return $result->setData(['success' => true,'data' => $params]);
            } catch (\Exception $e) {
                return $result->setData(['success' => false, 'error' => $e->getMessage()]);
            }
        }
        return $result->setData(['success' => false, 'error' => 'No item ID provided']);
    }
}