<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Helpdesk
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Helpdesk\Controller\Ticket;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;

/**
 * Webkul Marketplace Product Save Controller.
 */
class Loginpost extends Action
{
    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $_formKeyValidator;

    /**
     * @var \Webkul\Helpdesk\Helper\Tickets
     */
    protected $_ticketsHelper;

    /**
     * @var \Webkul\Helpdesk\Model\TicketsFactory
     */
    protected $_ticketsFactory;

    /**
     * @var \Webkul\Helpdesk\Logger\HelpdeskLogger
     */
    protected $_helpdeskLogger;

    /**
     * @var \Magento\Framework\Session\SessionManager
     */
    protected $_coreSession;

    /**
     * @param Context                                   $context
     * @param FormKeyValidator                          $formKeyValidator
     * @param \Webkul\Helpdesk\Helper\Tickets           $ticketsHelper
     * @param \Webkul\Helpdesk\Model\TicketsFactory     $ticketsFactory
     * @param \Webkul\Helpdesk\Logger\HelpdeskLogger    $helpdeskLogger
     * @param \Magento\Framework\Session\SessionManager $coreSession
     */
    public function __construct(
        Context $context,
        FormKeyValidator $formKeyValidator,
        \Webkul\Helpdesk\Helper\Tickets $ticketsHelper,
        \Webkul\Helpdesk\Model\TicketsFactory $ticketsFactory,
        \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger,
        \Magento\Framework\Session\SessionManager $coreSession
    ) {
        $this->_formKeyValidator = $formKeyValidator;
        $this->_ticketsHelper = $ticketsHelper;
        $this->_ticketsFactory = $ticketsFactory;
        $this->_helpdeskLogger = $helpdeskLogger;
        $this->_coreSession = $coreSession;
        parent::__construct(
            $context
        );
    }

    /**
     * Seller product save action.
     *
     * @return \Magento\Framework\Controller\Result\RedirectFactory
     */
    public function execute()
    {
        try {
            if ($this->getRequest()->isPost()) {
                if (!$this->_formKeyValidator->validate($this->getRequest())) {
                    $this->messageManager->addErrorMessage(__("Form key is not valid!!"));
                    return $this->resultRedirectFactory->create()->setPath(
                        '*/*/login',
                        ['_secure' => $this->getRequest()->isSecure()]
                    );
                }
                $data = $this->getRequest()->getParams();
                $ticketColl = $this->_ticketsFactory->create()->getCollection()
                    ->addFieldToFilter("entity_id", ["eq"=>$data["ticketid"]])
                    ->addFieldToFilter("email", ["eq"=>$data["email"]])
                    ->getFirstItem();
                if ($ticketColl->getId()) {
                    $data["id"] = $ticketColl->getEntityId();
                    $data["customer_id"] = $ticketColl->getCustomerId();
                    $this->_coreSession->setTsCustomer($data);
                    return $this->resultRedirectFactory->create()
                    ->setPath("helpdesk/ticket/view/", ["id"=>$ticketColl->getEntityId()]);
                } else {
                    $this->messageManager->addErrorMessage(
                        __("You entered incorrect data. Please enter correct data!! ")
                    );
                }
            } else {
                $this->messageManager->addErrorMessage(__("Unauthorised User!!"));
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->_helpdeskLogger->info($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__("Unable to login!!"));
            $this->_helpdeskLogger->info($e->getMessage());
        }
        return $this->resultRedirectFactory->create()->setPath(
            '*/*/login',
            ['_secure' => $this->getRequest()->isSecure()]
        );
    }
}
