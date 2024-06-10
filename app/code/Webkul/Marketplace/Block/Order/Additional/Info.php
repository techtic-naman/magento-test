<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Marketplace
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Marketplace\Block\Order\Additional;

class Info extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Sales\Model\Order\Item
     */
    protected $_item;

    /**
     * Set item
     *
     * @param \Magento\Sales\Model\Order\Item $item
     * @return $this
     */
    public function setItem(\Magento\Sales\Model\Order\Item $item)
    {
        $this->_item = $item;
        return $this;
    }

    /**
     * Get item
     *
     * @return \Magento\Sales\Model\Order\Item
     */
    public function getItem()
    {
        return $this->_item;
    }
}
