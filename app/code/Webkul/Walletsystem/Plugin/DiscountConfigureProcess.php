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

namespace Webkul\Walletsystem\Plugin;

/**
 * Class DiscountConfigureProcess
 *
 * Removes discount block when wallet amount product is in cart.
 */
class DiscountConfigureProcess
{
    /**
     * @var \Webkul\Walletsystem\Helper\Data
     */
    private $walletHelper;

    /**
     * Initialize dependencies
     *
     * @param \Webkul\Walletsystem\Helper\Data $walletHelper
     */
    public function __construct(
        \Webkul\Walletsystem\Helper\Data $walletHelper
    ) {
        $this->walletHelper = $walletHelper;
    }

    /**
     * Checkout LayoutProcessor before process plugin.
     *
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $LayoutProcessor
     * @param callable $proceed
     * @param Mixed $jsLayout
     * @return array
     */
    public function aroundProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $LayoutProcessor,
        callable $proceed,
        $jsLayout
    ) {
        $jsLayout = $proceed($jsLayout);
        if (!$this->walletHelper->getDiscountEnable() && !$this->walletHelper->getCartStatus()) {
            unset(
                $jsLayout['components']['checkout']['children']
                ['steps']['children']['billing-step']
                ['children']['payment']['children']
                ['afterMethods']['children']['discount']
            );
            unset(
                $jsLayout['components']['checkout']['children']
                ['steps']['children']['billing-step']
                ['children']['payment']['children']
                ['afterMethods']['children']['reward_amount']
            );
        }
        return $jsLayout;
    }
}
