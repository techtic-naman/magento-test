<?php

namespace Meetanshi\Bulksms\Controller\Adminhtml\Otp;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Meetanshi\Bulksms\Helper\Data;
use Magento\Framework\Controller\Result\JsonFactory;

class Developer extends Action
{
    private $helper;
    protected $resultJsonFactory;

    public function __construct(Context $context, Data $helper, JsonFactory $resultJsonFactory)
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helper = $helper;
        parent::__construct($context);
    }

    public function execute()
    {
        $rtn = $this->helper->sendDeveloperSms();
        $result = $this->resultJsonFactory->create();
        return $result->setData(['success' => true, 'responseText' => $rtn]);
    }

    protected function _isAllowed()
    {
        return true;
    }
}
