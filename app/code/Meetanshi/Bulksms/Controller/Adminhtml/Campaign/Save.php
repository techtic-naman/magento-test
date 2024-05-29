<?php

namespace Meetanshi\Bulksms\Controller\Adminhtml\Campaign;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session;
use Magento\Framework\View\Result\PageFactory;
use Meetanshi\Bulksms\Controller\Adminhtml\Bulksms;
use Meetanshi\Bulksms\Model\CampaignFactory;

class Save extends Bulksms
{
    protected $filter;
    protected $collectionFactory;
    protected $bulksms;
    protected $session;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CampaignFactory $bulksms,
        Session $session
    ) {
        parent::__construct($context, $resultPageFactory);
        $this->bulksms = $bulksms;
        $this->session = $session;
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
                }
                if (key_exists('grid', $data)) {
                    $data['phonebook_id'] = trim(str_replace('&', ',', $data['grid']), ',');
                } else {
                    $data['phonebook_id'] = implode(',', $data['phonebook_id']);
                }

                $model->setData($data);

                $model->save();
                $this->messageManager->addSuccessMessage(__('Campaign saved successfully.'));
                $this->session->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                    return;
                }
                $this->_redirect('*/*/');
                return;
            }
        } catch (\Exception $e) {
            \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info($e->getMessage());
        }

        $this->_getSession()->setFormData($data);
        $this->_redirect('*/*/edit', ['option_id' => $this->getRequest()->getParam('option_id')]);
        return;
    }
}
