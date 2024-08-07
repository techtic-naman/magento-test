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
namespace Webkul\Marketplace\Block\Adminhtml\Orders;

class Pay extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'Webkul_Marketplace::seller/pay.phtml';

    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    protected $_formFactory;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;

    /**
     * Constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Customer\Model\Customer        $customer
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        $this->_formFactory = $formFactory;
        $this->_customer = $customer;
        parent::__construct($context, $data);
    }

    /**
     * Get Customer.
     *
     * @return bool|\Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        $sellerId = $this->getRequest()->getParam('seller_id');
        if ($sellerId) {
            return $this->_customer->load($sellerId);
        }
        return false;
    }
}
