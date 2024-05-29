<?php

namespace Meetanshi\Bulksms\Controller\Adminhtml\Campaign;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Layout;
use Magento\Framework\View\Result\LayoutFactory;

/**
 * Class Products
 * @package Meetanshi\Bulksms\Controller\Adminhtml\Campaign
 */
class Contacts extends Action
{
    /**
     * @var LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * Products constructor.
     * @param Action\Context $context
     * @param LayoutFactory $resultLayoutFactory
     */
    public function __construct(
        Action\Context $context,
        LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($context);
        $this->resultLayoutFactory = $resultLayoutFactory;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }

    /**
     * @return ResponseInterface|ResultInterface|Layout
     */
    public function execute()
    {
        $resultLayout = $this->resultLayoutFactory->create();

        $resultLayout->getLayout()->getBlock('bulksms.edit.tab.phonebook')
                     ->setProducts($this->getRequest()->getPost('contact_products', null));

        return $resultLayout;
    }
}
