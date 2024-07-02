<?php
namespace Tryathome\Core\Controller\Index;

class Checkout extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Checkout Details'));
        // $resultPage->getLayout()->getBlock('tryathome.core.checkout');
        // Your custom logic here
        return $resultPage;
    }
}