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

namespace Webkul\Walletsystem\Controller\Plugin\Currency;

use Magento\Quote\Model\QuoteFactory;

/**
 * Webkul Walletsystem Class
 */
class SwitchAction
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Webkul\Walletsystem\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cartModel;

    /**
     * @var Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;

    /**
     * Initialize dependencies
     *
     * @param WebkulWalletsystemHelperData           $helper
     * @param MagentoCheckoutModelSession            $checkoutSession
     * @param MagentoCheckoutModelCart               $checkoutCartModel
     * @param MagentoStoreModelStoreManagerInterface $storeManager
     * @param MagentoFrameworkAppRequestHttp         $request
     * @param QuoteFactory                           $quoteFactory
     */
    public function __construct(
        \Webkul\Walletsystem\Helper\Data $helper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Checkout\Model\Cart $checkoutCartModel,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Request\Http $request,
        QuoteFactory $quoteFactory
    ) {
        $this->helper = $helper;
        $this->checkoutSession = $checkoutSession;
        $this->cartModel = $checkoutCartModel;
        $this->storeManager = $storeManager;
        $this->request = $request;
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * Around Execute
     *
     * @param \Magento\Directory\Controller\Currency\SwitchAction $subject
     * @param \Closure $proceed
     * @return array
     */
    public function aroundExecute(
        \Magento\Directory\Controller\Currency\SwitchAction $subject,
        \Closure $proceed
    ) {
        $this->checkoutSession->unsWalletDiscount();
        $storeManager = $this->storeManager;
        $prevCurrency = $storeManager->getStore()->getCurrentCurrencyCode();
        $result = $proceed();
        $currency = (string) $this->request->getParam('currency');
        $walletProductId = $this->helper->getWalletProductId();
        $currencySymbol = $this->helper->getCurrencySymbol(
            $storeManager->getStore()->getCurrentCurrencyCode()
        );
        $quote = '';
        if ($this->checkoutSession->getQuoteId()) {
            $quoteId = $this->checkoutSession->getQuoteId();
            $quote = $this->quoteFactory->create()
                ->load($quoteId);
        }
        if ($quote!='') {
            $cartData = $quote->getAllVisibleItems();
            if (!empty($cartData)) {
                foreach ($cartData as $cart) {
                    if ($cart->getProductId() == $walletProductId) {
                        $minimumAmount = $this->helper->getMinimumAmount();
                        $maximumAmount = $this->helper->getMaximumAmount();
                        if ($minimumAmount > $maximumAmount) {
                            $temp = $maximumAmount;
                            $maximumAmount = $minimumAmount;
                            $minimumAmount = $temp;
                        }
                        $finalPrice = $this->helper->getwkconvertCurrency(
                            $prevCurrency,
                            $currency,
                            $cart->getCustomPrice()
                        );
                        $finalminimumAmount = $this->helper->getwkconvertCurrency(
                            $prevCurrency,
                            $currency,
                            $minimumAmount
                        );
                        $finalmaximumAmount = $this->helper->getwkconvertCurrency(
                            $prevCurrency,
                            $currency,
                            $maximumAmount
                        );
                        if ($finalPrice > $finalmaximumAmount) {
                            $finalPrice = $finalmaximumAmount;
                        } elseif ($finalPrice < $finalminimumAmount) {
                            $finalPrice = $finalminimumAmount;
                        }
                        $this->wkCartSave($cart, $finalPrice);
                    }
                }
            }
        }
        $this->cartModel->save();
        if ($quote!='') {
            $quote->collectTotals()->save();
        }
        return $result;
    }

    /**
     * Save cart
     *
     * @param object $cart
     * @param int $finalPrice
     */
    public function wkCartSave($cart, $finalPrice)
    {
        $cart->setPrice($finalPrice);
        $cart->setCustomPrice($finalPrice);
        $cart->setOriginalCustomPrice($finalPrice);
        $cart->setRowTotal($finalPrice);
        $cart->save();
    }
}
