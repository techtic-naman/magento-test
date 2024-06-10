<?php
/**
 * Webkul Software.
 *
 * @category Webkul
 * @package  Webkul_Helpdesk
 * @author   Webkul
 * @license  https://store.webkul.com/license.html
 */
namespace Webkul\Helpdesk\Controller\Ticket;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlInterface;

/**
 * Webkul Marketplace Landing page Index Controller.
 */
class Newticket extends Action
{
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var UrlInterface
     */
    protected $urlInterface;

    /**
     * @param Context                                  $context
     * @param PageFactory                              $resultPageFactory
     * @param \Webkul\Helpdesk\Helper\Data             $dataHelper
     * @param \Webkul\Helpdesk\Helper\Tickets          $ticketHelper
     * @param \Webkul\Helpdesk\Model\CustomerFactory   $helpdeskCustomerFactory
     * @param \Magento\Customer\Model\Session          $mageCustomerSession
     * @param \Magento\\Framework\Url\EncoderInterface $urlEncoder
     * @param UrlInterface                             $urlInterface
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Webkul\Helpdesk\Helper\Data $dataHelper,
        \Webkul\Helpdesk\Helper\Tickets $ticketHelper,
        \Webkul\Helpdesk\Model\CustomerFactory $helpdeskCustomerFactory,
        \Magento\Customer\Model\Session $mageCustomerSession,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        UrlInterface $urlInterface
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_dataHelper = $dataHelper;
        $this->_ticketHelper = $ticketHelper;
        $this->_helpdeskCustomerFactory = $helpdeskCustomerFactory;
        $this->urlEncoder = $urlEncoder;
        $this->_mageCustomerSession = $mageCustomerSession;
        $this->urlInterface = $urlInterface;
        parent::__construct($context);
    }

    /**
     * Helpdesk Support page.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $this->_ticketHelper->unSetTsCustomerData();
        $loginRequired = $this->_dataHelper->getLoginRequired();
        if ($loginRequired) {
            $userId = $this->_ticketHelper->getTsCustomerId();

            if ($userId || $this->_mageCustomerSession->isLoggedIn()) {
                $resultPage = $this->_resultPageFactory->create();
                $resultPage->getConfig()->getTitle()->set(__("Create New Ticket"));
                return $resultPage;
            } else {
                return $this->resultRedirectFactory->create()->setPath(
                    'customer/account/login',
                    ['_secure' => $this->getRequest()->isSecure()]
                );
            }
        } else {
            $resultPage = $this->_resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->set(__("Create New Ticket"));
            return $resultPage;
        }
    }
}
