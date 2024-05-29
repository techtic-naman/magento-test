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
class Delete extends Action
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
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Webkul\Helpdesk\Model\ActivityRepository
     */
    protected $_activityRepo;

    /**
     * @var \Webkul\Helpdesk\Model\EventsRepository
     */
    protected $_eventsRepo;

    /**
     * @var \Webkul\Helpdesk\Model\TicketsFactory
     */
    protected $_ticketsFactory;

    /**
     * @var \Webkul\Helpdesk\Model\ThreadRepository
     */
    protected $_threadRepo;

    /**
     * @var \Webkul\Helpdesk\Helper\Tickets
     */
    protected $_ticketsHelper;

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
        $ticketId = $this->getRequest()->getParam("id");
        if ($ticketId > 0) {
            try {
                $userId = $this->_ticketsHelper->getTsCustomerId();
                if (!$userId) {
                    return $this->resultRedirectFactory->create()->setPath("helpdesk/ticket/login/");
                } else {
                    $ticketCollection = $this->_ticketsFactory->create()->getCollection()
                        ->addFieldToFilter('entity_id', ["eq"=>$ticketId])
                        ->addFieldToFilter('customer_id', ['eq'=>$userId]);
                    if (count($ticketCollection)) {
                        foreach ($ticketCollection as $ticket) {
                            $ticket_id = $ticket->getEntityId();
                            $this->_eventsRepo->checkTicketEvent("ticket", $ticket_id, "deleted");
                            $this->_activityRepo->saveActivity(
                                $ticket_id,
                                $ticket->getSubject(),
                                "delete",
                                "ticket"
                            );
                            $this->_threadRepo->deleteThreads($ticket_id);
                            $this->_threadRepo->unsetSplitTicket($ticket_id);
                            $ticket->delete();
                        }
                        $this->messageManager->addSuccessMessage(__("Ticket was successfully deleted"));
                    } else {
                        $this->messageManager->addErrorMessage(__("Unauthorised user!!"));
                    }
                }
            } catch (\Exception $e) {
                $this->_helpdeskLogger->info($e->getMessage());
            }
        }
        return $this->resultRedirectFactory->create()->setPath("helpdesk/ticket/mytickets/");
    }
}
