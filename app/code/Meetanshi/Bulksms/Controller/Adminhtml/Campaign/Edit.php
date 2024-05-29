<?php

namespace Meetanshi\Bulksms\Controller\Adminhtml\Campaign;

use Meetanshi\Bulksms\Controller\Adminhtml\Bulksms;

use Magento\Backend\App\Action\Context;
use Meetanshi\Bulksms\Model\Campaign;
use Magento\Framework\Registry;
use Magento\Backend\Model\Session;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Bulksms
{

    protected $resultPageFactory = false;
    protected $campaign;
    protected $registry;
    protected $session;

    public function __construct(
        Context $context,
        Registry $registry,
        Session $session,
        Campaign $campaign,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context, $resultPageFactory);
        $this->resultPageFactory = $resultPageFactory;
        $this->campaign = $campaign;
        $this->registry = $registry;
        $this->session = $session;
    }

    public function execute()
    {

        $id = $this->getRequest()->getParam('id');
        $model = $this->campaign;

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This row no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $data = $this->session->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->registry->register('campaign_manage', $model);
        $this->_view->loadLayout();
        $this->_view->getPage()->getConfig()->getTitle()->set(__('Manage Campaign'));
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }
}
