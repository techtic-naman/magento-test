<?php

namespace Tryathome\Core\Controller\Index;

class Customerdetail extends \Magento\Framework\App\Action\Action
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

        // Create a result page
        $resultPage = $this->_pageFactory->create();

        $block = $resultPage->getLayout()->getBlock('tryathome.core.customerdetail');

        // Set the data to the result page
        $resultPage->getConfig()->getTitle()->set(__('Trial Details'));
        $resultPage->getLayout()->getBlock('tryathome.core.customerdetail');

        return $resultPage;
    }
    
}