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
namespace Webkul\Helpdesk\Model;

use \Magento\Framework\Exception\CouldNotSaveException;

class ThreadRepository implements \Webkul\Helpdesk\Api\ThreadRepositoryInterface
{
    /**
     * @var \Webkul\Helpdesk\Model\CustomerFactory
     */
    protected $_helpdeskCustomerFactory;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * @var \Webkul\Helpdesk\Model\ThreadFactory
     */
    protected $_threadFactory;

    /**
     * @var \Webkul\Helpdesk\Model\TicketsFactory
     */
    protected $_ticketsFactory;

    /**
     * @var \Webkul\Helpdesk\Logger\HelpdeskLogger
     */
    protected $_helpdeskLogger;

    /**
     * @var \Magento\User\Model\UserFactory
     */
    protected $_userFactory;

    /**
     * @var \Webkul\Helpdesk\Model\ResourceModel\ThreadFactory
     */
    protected $_resThreadFactory;

    /**
     * TicketsRepository constructor.
     *
     * @param \Webkul\Helpdesk\Model\CustomerFactory             $helpdeskCustomerFactory
     * @param \Magento\Backend\Model\Auth\Session                $authSession
     * @param \Webkul\Helpdesk\Model\ThreadFactory               $threadFactory
     * @param \Webkul\Helpdesk\Model\TicketsFactory              $ticketsFactory
     * @param \Webkul\Helpdesk\Logger\HelpdeskLogger             $helpdeskLogger
     * @param \Magento\User\Model\UserFactory                    $userFactory
     * @param \Webkul\Helpdesk\Model\ResourceModel\ThreadFactory $resThreadFactory
     */
    public function __construct(
        \Webkul\Helpdesk\Model\CustomerFactory $helpdeskCustomerFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Webkul\Helpdesk\Model\ThreadFactory $threadFactory,
        \Webkul\Helpdesk\Model\TicketsFactory $ticketsFactory,
        \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger,
        \Magento\User\Model\UserFactory $userFactory,
        \Webkul\Helpdesk\Model\ResourceModel\ThreadFactory $resThreadFactory
    ) {
        $this->_helpdeskCustomerFactory = $helpdeskCustomerFactory;
        $this->_authSession = $authSession;
        $this->_threadFactory = $threadFactory;
        $this->_ticketsFactory = $ticketsFactory;
        $this->_helpdeskLogger = $helpdeskLogger;
        $this->_userFactory = $userFactory;
        $this->_resThreadFactory = $resThreadFactory;
    }

    /**
     * CreateThread Create thread function
     *
     * @param  int   $ticketId  Ticket Id
     * @param  mixed $wholedata Post request data
     * @return int  $threadId Thread Id
     */
    public function createThread($ticketId, $wholedata)
    {
        try {
            $thread = $this->_threadFactory->create();
            $thread->setTicketId($ticketId);
            $whois = 1;
            if (isset($wholedata["checkwhois"]) && $wholedata["checkwhois"] == 1) {
                if (isset($wholedata["is_admin"]) && $wholedata["is_admin"] == 1) {
                    $thread->setWhoIs("admin");
                } else {
                    $thread->setWhoIs("agent");
                }
                $adminId  = $this->_authSession->getUser()->getId();
                if ($adminId == 0) {
                    $agent = $this->_authSession->getUser();
                    $thread->setSender($agent->getName());
                } else {
                    $agent = $this->_userFactory->create()->load($adminId);
                    $thread->setSender($agent->getName());
                }
            } else {
                $ticket = $this->_ticketsFactory->create()->load($ticketId);
                $customer = $this->_helpdeskCustomerFactory->create()->load($ticket->getCustomerId());
                $thread->setSender($customer->getName());
                $thread->setWhoIs("customer");
            }
            if (isset($wholedata["to"]) && $wholedata["to"] != "") {
                $to = implode(',', array_filter(explode(',', $wholedata["to"])));
                $thread->setTo($to);
            }
            if (isset($wholedata["cc"]) && $wholedata["cc"] != "") {
                $cc = implode(',', array_filter(explode(',', $wholedata["cc"])));
                $thread->setCc($cc);
            }
            if (isset($wholedata["bcc"]) && $wholedata["bcc"] != "") {
                $bcc = implode(',', array_filter(explode(',', $wholedata["bcc"])));
                $thread->setBcc($bcc);
            }
            if (isset($wholedata["source"])) {
                $thread->setSource($wholedata["source"]);
            }
            $thread->setThreadType($wholedata["thread_type"]);
            $thread->setReply(htmlentities($wholedata["query"]));
            $threadId = $thread->save()->getId();
            return $threadId;
        } catch (\Exception $e) {
            $this->_helpdeskLogger->info($e->getMessage());
            throw new CouldNotSaveException(__('Unable to save thread'), $e);
        }
    }

    /**
     * DeleteThreads Delete thread from ticket
     *
     * @param int $ticketId Ticket Id
     */
    public function deleteThreads($ticketId)
    {
        $collection = $this->_threadFactory->create()->getCollection()
            ->addFieldToFilter("ticket_id", $ticketId);
        foreach ($collection as $key => $thread) {
            $thread->delete();
        }
    }

    /**
     * GetTicketIdByThreadId Return ticket id by thread id
     *
     * @param int $threadId Thread id
     */
    public function getTicketIdByThreadId($threadId)
    {
        return $this->_threadFactory->create()->load($threadId)->getTicketId();
    }

    /**
     * UnsetSplitTicket
     *
     * @param int $ticketId
     */
    public function unsetSplitTicket($ticketId)
    {
        $collection = $this->_threadFactory->create()->getCollection()
            ->addFieldToFilter("split_ticket", $ticketId);
        if ($collection->getSize()) {
            $resThread = $this->_resThreadFactory->create();
            $thread = $collection->getFirstItem();
            $thread->setSplitTicket(null);
            $resThread->save($thread);
        }
    }
}
