<?php

namespace Meetanshi\Bulksms\Controller\Adminhtml\Campaign;

use Meetanshi\Bulksms\Controller\Adminhtml\Bulksms;
use Meetanshi\Bulksms\Model\CampaignFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Delete extends Bulksms
{

    protected $collectionFactory;
    protected $bulksms;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CampaignFactory $bulksms
    ) {
        parent::__construct($context, $resultPageFactory);
        $this->bulksms = $bulksms;
    }

    public function execute()
    {
        $data = $this->getRequest()->getParams();
        try {

            if ($data) {

                $model = $this->bulksms->create();
                $id = $this->getRequest()->getParam('id');
                if ($id) {
                    $model->load($id);
                    $model->delete();
                    $this->messageManager->addSuccessMessage(__('Campaign deleted successfully.'));
                    $this->_redirect('*/*/');
                    return;
                }

            }

        } catch (\Exception $e) {
            \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info($e->getMessage());
        }

        $this->messageManager->addErrorMessage(__('Something went wrong.'));
        $this->_redirect('*/*/');
        return;
    }
}
