<?php

namespace Meetanshi\Bulksms\Controller\Adminhtml\Smslog;

use Meetanshi\Bulksms\Controller\Adminhtml\Bulksms;
use Magento\Ui\Component\MassAction\Filter;
use Meetanshi\Bulksms\Model\ResourceModel\Smslog\CollectionFactory;
use Meetanshi\Bulksms\Model\SmslogFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Massdelete extends Bulksms
{

    protected $filter;
    protected $resultPageFactory;
    protected $collectionFactory;
    protected $smslog;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Filter $filter,
        SmslogFactory $smslog,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context, $resultPageFactory);
        $this->resultPageFactory = $resultPageFactory;
        $this->filter = $filter;
        $this->smslog = $smslog;
        $this->collectionFactory = $collectionFactory;
    }

    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $productsUpdated = 0;
            foreach ($collection as $item) {

                $smslogItem = $this->smslog->create()->load($item['id']);
                $smslogItem->delete();
                $productsUpdated++;
            }
            if ($productsUpdated) {
                $this->messageManager->addSuccess(__('A total of %1 record(s) were deleted.', $productsUpdated));
            }

        } catch (\Exception $e) {
            \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info($e->getMessage());
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }
}
