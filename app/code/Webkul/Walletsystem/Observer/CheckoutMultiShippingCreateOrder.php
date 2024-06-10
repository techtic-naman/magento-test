<?php
/**
 * Webkul Software
 *
 * @category Webkul
 * @package Webkul_Walletsystem
 * @author Webkul
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */

namespace Webkul\Walletsystem\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Webkul Walletsystem Class
 */
class CheckoutMultiShippingCreateOrder implements ObserverInterface
{
    /**
     * Walletsystem event handler
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $walletAmount = $observer->getAddress()->getWalletAmount();
        $baseWalletAmount = $observer->getAddress()->getBaseWalletAmount();
        $order = $observer->getOrder();
        $order->setWalletAmount($walletAmount);
        $order->setBaseWalletAmount($baseWalletAmount);
    }
}
