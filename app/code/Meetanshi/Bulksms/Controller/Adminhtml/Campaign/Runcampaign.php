<?php

namespace Meetanshi\Bulksms\Controller\Adminhtml\Campaign;

use Meetanshi\Bulksms\Controller\Adminhtml\Bulksms;
use Meetanshi\Bulksms\Model\CampaignFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Meetanshi\Bulksms\Model\BulksmsFactory;
use Meetanshi\Bulksms\Helper\Data;

class Runcampaign extends Bulksms
{
    protected $campaignFactory;
    protected $bulksmsFactory;
    protected $helper;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        BulksmsFactory $bulksmsFactory,
        Data $helper,
        CampaignFactory $campaignFactory
    ) {
        parent::__construct($context, $resultPageFactory);
        $this->campaignFactory = $campaignFactory;
        $this->bulksmsFactory = $bulksmsFactory;
        $this->helper = $helper;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        try {
            if ($id) {

                $campaignItem = $this->campaignFactory->create()->load($id);

                if ($campaignItem->getId()) {
                    $storename = $this->helper->getStoreName();
                    $storeUrl = $this->helper->getStoreUrl();
                    $message = $campaignItem->getMessage();

                    $replaceArray = [$storename, $storeUrl];
                    $originalArray = ['{{shop_name}}', '{{shop_url}}'];
                    $newMessage = str_replace($originalArray, $replaceArray, $message);

                    $phonebookId = explode(',', $campaignItem->getPhonebookId());
                    foreach ($phonebookId as $phoneId) {

                        $phonebook = $this->bulksmsFactory->create()->load($phoneId);
                        if ($phonebook->getId()) {

                            $name = $phonebook->getName();
                            $replaceArraySec = [$name];
                            $originalArraySec = ['{{username}}'];
                            $newMessage = str_replace($originalArraySec, $replaceArraySec, $newMessage);

                            $this->helper->curlApi($phonebook->getMobilenumber(), $newMessage);
                        }
                    }
                    $this->messageManager->addSuccessMessage(__('Campaign ran successfully.'));
                }
            }
        } catch (\Exception $e) {
            \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info($e->getMessage());
            $this->messageManager->addErrorMessage(__($e->getMessage()));
        }
        $this->_redirect('*/*/');
        return;
    }
}
