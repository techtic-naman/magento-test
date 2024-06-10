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

namespace Webkul\Walletsystem\Block\Adminhtml\Wallet\Renderer;

/**
 * Webkul Walletsystem PriceFormatter Block
 */
class PriceFormatter extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Array to store all options data.
     *
     * @var array
     */
    protected $actions = [];
    
    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * Return Actions.
     *
     * @param \Magento\Framework\DataObject $row
     *
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
      
        $currency = $this->storeManager->getStore()->getBaseCurrency()->getCurrencySymbol();
        $amount = !empty($row->getRemainingAmount())?$row->getRemainingAmount():0.00;
        return $currency.$amount;
    }
}
