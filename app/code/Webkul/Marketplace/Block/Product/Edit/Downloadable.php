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

namespace Webkul\Marketplace\Block\Product\Edit;

class Downloadable extends \Magento\Framework\View\Element\Template
{
    /**
     * \Magento\Framework\Registry.
     * @var Registry
     */
    protected $_registry;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Get product
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getSellerProduct()
    {
        return $this->_registry->registry('product');
    }
}
