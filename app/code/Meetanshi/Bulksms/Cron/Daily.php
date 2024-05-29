<?php

namespace Meetanshi\Bulksms\Cron;

use Meetanshi\Bulksms\Model\ResourceModel\Campaign\CollectionFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\ObjectManager\ConfigLoaderInterface;
use Meetanshi\Bulksms\Model\BulksmsFactory;
use Meetanshi\Bulksms\Helper\Data;

class Daily
{
    protected $campaignCollection;
    protected $bulksmsFactory;
    protected $helper;

    public function __construct(
        CollectionFactory $campaignCollection,
        ObjectManagerInterface $objectManager,
        BulksmsFactory $bulksmsFactory,
        Data $helper,
        ConfigLoaderInterface $configLoader
    ) {
        $this->campaignCollection = $campaignCollection;
        $this->bulksmsFactory = $bulksmsFactory;
        $this->helper = $helper;
        $objectManager->configure($configLoader->load('frontend'));
    }

    public function execute()
    {
        if ($this->helper->isEnable()) {
            try {
                $today = date("Y-m-d");
                $hour = date('g');

                $collections = $this->campaignCollection->create()->addFieldToFilter('startdate', $today);
                $collections->addfieldtofilter('status', 1);
                $collections->addfieldtofilter('hour', $hour);

                foreach ($collections as $campaignItem) {

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
                }
            } catch (\Exception $e) {
                \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug($e->getMessage());
            }
        }
    }
}
