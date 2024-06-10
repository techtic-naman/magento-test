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

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Splitthread extends \Magento\Backend\App\Action
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
     * @param Context                                          $context
     * @param PageFactory                                      $resultPageFactory
     * @param \Webkul\Helpdesk\Model\ThreadFactory             $threadFactory
     * @param \Webkul\Helpdesk\Model\TicketsFactory            $ticketsFactory
     * @param \Magento\Backend\Model\Auth\Session              $authSession
     * @param \Webkul\Helpdesk\Model\ActivityRepository        $activityRepo
     * @param \Webkul\Helpdesk\Model\EventsRepository          $eventsRepo
     * @param \Webkul\Helpdesk\Model\TicketsRepository         $ticketsRepo
     * @param \Webkul\Helpdesk\Logger\HelpdeskLogger           $helpdeskLogger
     * @param \Webkul\Helpdesk\Helper\Data                     $helper
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Webkul\Helpdesk\Model\ThreadFactory $threadFactory,
        \Webkul\Helpdesk\Model\TicketsFactory $ticketsFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Webkul\Helpdesk\Model\ActivityRepository $activityRepo,
        \Webkul\Helpdesk\Model\EventsRepository $eventsRepo,
        \Webkul\Helpdesk\Model\TicketsRepository $ticketsRepo,
        \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger,
        \Webkul\Helpdesk\Helper\Data $helper,
        \Magento\Framework\Serialize\SerializerInterface $serializer
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_threadFactory = $threadFactory;
        $this->_ticketsFactory = $ticketsFactory;
        $this->_authSession = $authSession;
        $this->_activityRepo = $activityRepo;
        $this->_eventsRepo = $eventsRepo;
        $this->_ticketsRepo = $ticketsRepo;
        $this->_helpdeskLogger = $helpdeskLogger;
        $this->_helper = $helper;
        $this->serializer = $serializer;
    }

    /**
     * Split thread
     *
     * @return void
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam("id");
        $thread = $this->_threadFactory->create()->load($id);
        $ticketId = $thread->getTicketId();
        $ticket = $this->_ticketsFactory->create()->load($ticketId);
        $data = [];
        $data['fullname'] = $ticket->getFullname();
        $data['email'] = $ticket->getEmail();
        $data['subject'] = $ticket->getSubject();
        $data['query'] = $thread->getReply();
        $data['source'] = "website";
        $data['who_is'] = "admin";
        $data['type'] = $this->_helper->getTicketDefaultType();
        $ticketId = $this->_ticketsRepo->createTicket($data);
        $thread->setSplitTicket($ticketId);
        $thread->save();
        $threadNew = $this->_threadFactory->create();
        $threadNew->setTicketId($ticketId);
        $threadNew->setSender($thread->getSender());
        $threadNew->setSource('website');
        $threadNew->setAttachment($thread->getAttachment());
        $threadNew->setWhoIs($data['who_is']);
        $threadNew->setThreadType("create");
        $threadNew->setReply($data['query']);
        $threadNew->save();
        $url = $this->getTicketUrl($ticketId);
        $this->_activityRepo->saveActivity($ticketId, "", "add", "ticket");
        $this->_eventsRepo->checkTicketEvent("ticket", $ticketId, "created");
        $response = $this->serializer->serialize(['url' => $url, 'id' => $ticketId]);
        $this->getResponse()->setBody($response);
    }

    /**
     * Check splitthread Events Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Helpdesk::tickets');
    }

    /**
     * GetTicketUrl
     *
     * @param int $Id
     */
    private function getTicketUrl($Id)
    {
        $path = 'helpdesk/ticketsmanagement_tickets/viewreply';
        return $this->_backendUrl->getUrl($path,['id' => $Id]);
    }
}
