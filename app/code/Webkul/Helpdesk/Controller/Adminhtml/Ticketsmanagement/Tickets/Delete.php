<?php
/**
 * Webkul Software.
 *
 * @category Webkul
 * @package  Webkul_Helpdesk
 * @author   Webkul
 * @license  https://store.webkul.com/license.html
 */
namespace Webkul\Helpdesk\Controller\Adminhtml\Ticketsmanagement\Tickets;

use Magento\Framework\Exception\AuthenticationException;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Webkul\Helpdesk\Logger\HelpdeskLogger
     */
    protected $_helpdeskLogger;

    /**
     * @param Context                                   $context
     * @param PageFactory                               $resultPageFactory
     * @param \Magento\Backend\Model\Auth\Session       $authSession
     * @param \Webkul\Helpdesk\Model\ActivityRepository $activityRepo
     * @param \Webkul\Helpdesk\Logger\HelpdeskLogger    $helpdeskLogger
     * @param \Webkul\Helpdesk\Model\EventsRepository   $eventsRepo
     * @param \Webkul\Helpdesk\Model\TicketsFactory     $ticketsFactory
     * @param \Webkul\Helpdesk\Model\ThreadRepository   $threadRepo
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Webkul\Helpdesk\Model\ActivityRepository $activityRepo,
        \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger,
        \Webkul\Helpdesk\Model\EventsRepository $eventsRepo,
        \Webkul\Helpdesk\Model\TicketsFactory $ticketsFactory,
        \Webkul\Helpdesk\Model\ThreadRepository $threadRepo
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_authSession = $authSession;
        $this->_activityRepo = $activityRepo;
        $this->_eventsRepo = $eventsRepo;
        $this->_ticketsFactory = $ticketsFactory;
        $this->_threadRepo = $threadRepo;
    }

    /**
     * Delete Ticket
     *
     * @return void
     */
    public function execute()
    {
        $ticketId = $this->getRequest()->getParam("id");
        if ($ticketId > 0) {
            try {
                $this->_eventsRepo->checkTicketEvent("ticket", $ticketId, "deleted");
                $model = $this->_ticketsFactory->create()->load($ticketId);
                $this->_activityRepo->saveActivity($model->getId(), $model->getSubject(), "delete", "ticket");
                $model->delete();
                $this->_threadRepo->deleteThreads($ticketId);
                $this->_threadRepo->unsetSplitTicket($ticketId);
                $this->messageManager->addSuccessMessage(__("Ticket was successfully deleted"));
                return $this->resultRedirectFactory->create()->setPath('*/*/');
            } catch (\Exception $e) {
                $this->_helpdeskLogger->info($e->getMessage());
                return $this->resultRedirectFactory->create()->setPath("*/*/edit", ["id" => $ticketId]);
            }
        }
    }

    /**
     * Check delete ticket Helpdesk Events Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Helpdesk::tickets');
    }
}
