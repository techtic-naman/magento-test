<?php
namespace Tryathome\Core\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class SetCustomPrice implements ObserverInterface
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        $this->logger->info('SetCustomPrice observer executed');
        // $item = $observer->getEvent()->getData('quote_item');
        // $item = ($item->getParentItem() ? $item->getParentItem() : $item);
        
        // // Assuming $params is accessible or you retrieve the trial flag differently
        // // For example, using a custom option or item data
        // $params = $item->getProduct()->getCustomOption('additional_options');
        // $this->logger->info('Params: ' . print_r($params, true));
        // if ($params && isset($params['trial'])) {
        //     $this->logger->info('Setting custom price to 0 for item: ' . $item->getProduct()->getName());
        //     $customPrice = 0; // Set your custom price here
            
        //     $item->setCustomPrice($customPrice);
        //     $item->setOriginalCustomPrice($customPrice);
        //     $item->getProduct()->setIsSuperMode(true);
        // } else {
        //     $this->logger->info('Custom price not set, trial param not found for item: ' . $item->getProduct()->getName());
        // }
        // $item = $observer->getEvent()->getData('quote_item');         
        // $item = ( $item->getParentItem() ? $item->getParentItem() : $item );
        // $price = 0; //set your price here
        // $this->logger->info('price set');
        // $item->setCustomPrice($price);
        // $this->logger->info('price get');
        // $item->setOriginalCustomPrice($price);
        // $item->getProduct()->setIsSuperMode(true);


        $item = $observer->getEvent()->getData('quote_item');         
        $item = ($item->getParentItem() ? $item->getParentItem() : $item);
        $product = $item->getProduct();
    
        // Assuming the trial information is stored in a product custom option
        $options = $product->getCustomOptions();
        $trialOption = false;
    
        foreach ($options as $option) {
            if ($option->getCode() == 'additional_options') {
                $additionalOptions = json_decode($option->getValue(), true);
                if (isset($additionalOptions['trial'])) {
                    $trialOption = true;
                    $this->logger->info('Trial option found for item: ' . $product->getName());
                    break;
                }
            }
        }
    
        if ($trialOption) {
            $price = 0; // Set your price here
            $this->logger->info('Setting custom price to 0 for item: ' . $product->getName());
            $item->setCustomPrice($price);
            $item->setOriginalCustomPrice($price);
            $item->getProduct()->setIsSuperMode(true);
        } else {
            $this->logger->info('Trial option not found for item: ' . $product->getName());
        }
    }
}