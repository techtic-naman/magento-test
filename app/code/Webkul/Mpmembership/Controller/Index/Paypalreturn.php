<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Mpmembership
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Mpmembership\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Webkul\Mpmembership\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Customer\Model\CustomerFactory;

/**
 *  Webkul Mpmembership Index Ipnnotifyproduct controller
 */
class Paypalreturn extends Action implements \Magento\Framework\App\CsrfAwareActionInterface
{
    /**
     * @var \Webkul\Mpmembership\Model\ResourceModel\Product\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Webkul\Mpmembership\Model\Product
     */
    protected $membershipModel;

    /**
     * @var \Webkul\Mpmembership\Helper\Data
     */
    protected $helper;
    
    /**
     * @var \Magento\Customer\Model\Session
     */
    public $customerSession;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    public $customer;

    /**
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param \Webkul\Mpmembership\Model\Product $membershipModel
     * @param \Webkul\Mpmembership\Helper\Data $helper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\CustomerFactory $customer
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        \Webkul\Mpmembership\Model\Product $membershipModel,
        \Webkul\Mpmembership\Helper\Data $helper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\CustomerFactory $customer
    ) {
        parent::__construct($context);
        $this->collectionFactory    = $collectionFactory;
        $this->membershipModel      = $membershipModel;
        $this->helper               = $helper;
        $this->customerSession      = $customerSession;
        $this->customer             = $customer;
    }

    /**
     * @inheritDoc
     */
    public function createCsrfValidationException(
        RequestInterface $request
    ): ?InvalidRequestException {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * Executes when paypal returns to website after completing payment
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        try {
            $data = $this->getRequest()->getParams();
            
            $inv = $data['invoice'];
            $specialchar = "-";
            strtok($inv, $specialchar);
            $result = strtok("");
            $customerID = (int)$result;

            $productIds = [];

            if (isset($data['ref_no'])) {
                $invoice = explode('-', $data['ref_no']);
                if (isset($data['pro_ids'])) {
                    $productIds = json_decode(urldecode($data['pro_ids']));
                }

                $transdata = [
                    'seller_id' => $invoice[1] ,
                    'reference_number' => $invoice[0],
                    'transaction_status' => 'pending'
                ];

                if (!empty($productIds)) {
                    $transdata['no_of_products'] = count($productIds);
                    $transdata['product_ids'] = implode(",", $productIds);
                }
                $membershipdata = $this->collectionFactory->create();
                $collection = $membershipdata->addFieldToFilter(
                    'reference_number',
                    ['eq' => $invoice[0]]
                );

                if ($collection->getSize() <= 0) {
                    $sellerTransactionCollection = $this->membershipModel
                        ->setData($transdata)
                        ->save();
                }
            }
        } catch (\Exception $e) {
            $this->helper->logDataInLogger("Controller_Index_Paypalreturn execute : ".$e->getMessage());
        }
            // Load customer
            $customer = $this->customer->create()->load($customerID);
            // Load customer session
            $this->customerSession->setCustomerAsLoggedIn($customer);

        if ($this->customerSession->isLoggedIn()) {
            $this->_redirect('mpmembership/index/index');
        } else {
            $this->_redirect('mpmembership');
        }
    }
}
