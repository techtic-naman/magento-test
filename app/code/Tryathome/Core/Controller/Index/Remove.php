<?php

namespace Tryathome\Core\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Checkout\Model\Cart;

class Remove extends Action
{
    protected $cart;
    protected $resultJsonFactory;

    public function __construct(
        Context $context,
        Cart $cart,
        JsonFactory $resultJsonFactory
    ) {
        $this->cart = $cart;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $itemId = $this->getRequest()->getParam('item_id');
        if ($itemId) {
            try {
                $this->cart->removeItem($itemId)->save();
                return $result->setData(['success' => true]);
            } catch (\Exception $e) {
                return $result->setData(['success' => false, 'error' => $e->getMessage()]);
            }
        }
        return $result->setData(['success' => false, 'error' => 'No item ID provided']);
    }
}