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
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;

/**
 * Webkul Marketplace Product Save Controller.
 */
class Massdelete extends Action
{
    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $_formKeyValidator;

    /**
     * @var \Webkul\Helpdesk\Logger\HelpdeskLogger
     */
    protected $_helpdeskLogger;

    /**
     * @param Context                                    $context
     * @param FormKeyValidator                           $formKeyValidator
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Webkul\Helpdesk\Model\ActivityRepository  $activityRepo
     * @param \Webkul\Helpdesk\Model\EventsRepository    $eventsRepo
     * @param \Webkul\Helpdesk\Model\TicketsFactory      $ticketsFactory
     * @param \Webkul\Helpdesk\Model\ThreadRepository    $threadRepo
     * @param \Webkul\Helpdesk\Helper\Tickets            $ticketsHelper
     * @param \Webkul\Helpdesk\Logger\HelpdeskLogger     $helpdeskLogger
     */
    public function __construct(
        Context $context,
        FormKeyValidator $formKeyValidator,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Webkul\Helpdesk\Model\ActivityRepository $activityRepo,
        \Webkul\Helpdesk\Model\EventsRepository $eventsRepo,
        \Webkul\Helpdesk\Model\TicketsFactory $ticketsFactory,
        \Webkul\Helpdesk\Model\ThreadRepository $threadRepo,
        \Webkul\Helpdesk\Helper\Tickets $ticketsHelper,
        \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger
    ) {
        parent::__construct($context);
        $this->_formKeyValidator = $formKeyValidator;
        $this->resultPageFactory = $resultPageFactory;
        $this->_activityRepo = $activityRepo;
        $this->_eventsRepo = $eventsRepo;
        $this->_ticketsFactory = $ticketsFactory;
        $this->_threadRepo = $threadRepo;
        $this->_ticketsHelper = $ticketsHelper;
        $this->_helpdeskLogger = $helpdeskLogger;
    }

    /**
     * Seller product save action.
     *
     * @return \Magento\Framework\Controller\Result\RedirectFactory
     */
    public function execute()
    {
        try {
            $userId = $this->_ticketsHelper->getTsCustomerId();
            if (!$userId) {
                return $this->resultRedirectFactory->create()->setPath('helpdesk/ticket/login');
            } else {
                $unauth_ids = [];
                if ($this->getRequest()->isPost()) {
                    if (!$this->_formKeyValidator->validate($this->getRequest())) {
                        $this->messageManager->addError(__("Form key is not valid!!"));
                        return $this->resultRedirectFactory->create()->setPath(
                            '*/*/',
                            ['_secure' => $this->getRequest()->isSecure()]
                        );
                    }
                    $ids = $this->getRequest()->getParam('mass_delete');
                    $deleteIds = [];
                    $ticketCollection = $this->_ticketsFactory->create()->getCollection()
                        ->addFieldToFilter('entity_id', ["in"=>$ids])
                        ->addFieldToFilter('customer_id', ['eq'=>$userId]);
                    foreach ($ticketCollection as $ticket) {
                        $ticket_id = $ticket->getEntityId();
                        $this->_eventsRepo->checkTicketEvent("ticket", $ticket_id, "deleted");
                        $this->_activityRepo->saveActivity(
                            $ticket_id,
                            $ticket->getSubject(),
                            "delete",
                            "ticket"
                        );
                        array_push($deleteIds, $ticket_id);
                        $this->_threadRepo->deleteThreads($ticket_id);
                        $this->_threadRepo->unsetSplitTicket($ticket_id);
                        $ticket->delete();
                    }
                    $unauth_ids = $deleteIds ? array_diff($ids, $deleteIds) : "";
                }
                if ($unauth_ids) {
                    if (count($unauth_ids)) {
                        $this->messageManager->addErrorMessage(
                            __(
                                'You are not authorized to delete 
                    tickets with id '.implode(",", $unauth_ids)
                            )
                        );
                    }
                } else {
                    $this->messageManager->addSuccessMessage(__('Tickets has been successfully deleted from your account'));
                }
            }
            return $this->resultRedirectFactory->create()->setPath('helpdesk/ticket/mytickets');
        } catch (\Exception $e) {
            $this->_helpdeskLogger->info($e->getMessage());
            return $this->resultRedirectFactory->create()->setPath('*/*/mytickets');
        }
    }
}
