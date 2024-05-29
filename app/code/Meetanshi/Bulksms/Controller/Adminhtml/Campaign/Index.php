<?php

namespace Meetanshi\Bulksms\Controller\Adminhtml\Campaign;

use Meetanshi\Bulksms\Controller\Adminhtml\Bulksms;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Bulksms
{

    protected $resultPageFactory = false;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context, $resultPageFactory);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend((__('Manage Campaigns')));
        return $resultPage;
    }
}
