<?php

namespace Meetanshi\Bulksms\Controller\Adminhtml\Campaign;

use Meetanshi\Bulksms\Controller\Adminhtml\Bulksms;
use Magento\Ui\Component\MassAction\Filter;
use Meetanshi\Bulksms\Model\ResourceModel\Campaign\CollectionFactory;
use Meetanshi\Bulksms\Model\CampaignFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Massdelete extends Bulksms
{

    protected $filter;
    protected $resultPageFactory;
    protected $collectionFactory;
    protected $bulksms;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Filter $filter,
        CampaignFactory $bulksms,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context, $resultPageFactory);
        $this->resultPageFactory = $resultPageFactory;
        $this->filter = $filter;
        $this->bulksms = $bulksms;
        $this->collectionFactory = $collectionFactory;
    }

    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $productsUpdated = 0;
            foreach ($collection as $item) {

                $bulksmsItem = $this->bulksms->create()->load($item['id']);
                $bulksmsItem->delete();
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
