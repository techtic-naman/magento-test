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
namespace Webkul\Helpdesk\Controller\Adminhtml\Ticketsmanagement\Tickets;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Webkul\Helpdesk\Model\ResourceModel\Ticketsla\CollectionFactory;
use Webkul\Helpdesk\Model\ResourceModel\Slapolicy\CollectionFactory as SlapolicyCollFactory;

class Changeproperty extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Webkul\Helpdesk\Logger\HelpdeskLogger
     */
    protected $_helpdeskLogger;

    /**
     * @var \Webkul\Helpdesk\Model\TicketsFactory
     */
    protected $_ticketsFactory;

    /**
     * @var \Webkul\Helpdesk\Model\EventsRepository
     */
    protected $_eventsRepo;

    /**
     * @var \Webkul\Helpdesk\Model\SlaRepository
     */
    protected $slaRepo;

    /**
     * @var CollectionFactory
     */
    protected $ticketSlaCollFactory;

    /**
     * @var SlapolicyCollFactory
     */
    protected $slapolicyCollFactory;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * @var \Webkul\Helpdesk\Model\TicketslaRepository
     */
    protected $ticketslaRepo;

    /**
     * @var \Webkul\Helpdesk\Model\TicketslaRepository
     */
    protected $ticketHelper;

    /**
     * @param Context                                           $context
     * @param PageFactory                                       $resultPageFactory
     * @param \Webkul\Helpdesk\Model\TicketsFactory             $ticketsFactory
     * @param \Webkul\Helpdesk\Model\EventsRepository           $eventsRepo
     * @param \Webkul\Helpdesk\Logger\HelpdeskLogger            $helpdeskLogger
     * @param \Webkul\Helpdesk\Model\TicketslaRepository        $ticketslaRepo
     * @param \Magento\Framework\Serialize\SerializerInterface  $serializer
     * @param \Webkul\Helpdesk\Model\SlaRepository              $slaRepo
     * @param \Webkul\Helpdesk\Helper\Tickets                   $ticketHelper
     * @param CollectionFactory                                 $ticketSlaCollFactory
     * @param SlapolicyCollFactory                              $slapolicyCollFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Webkul\Helpdesk\Model\TicketsFactory $ticketsFactory,
        \Webkul\Helpdesk\Model\EventsRepository $eventsRepo,
        \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger,
        \Webkul\Helpdesk\Model\TicketslaRepository $ticketslaRepo,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Webkul\Helpdesk\Model\SlaRepository $slaRepo,
        \Webkul\Helpdesk\Helper\Tickets $ticketHelper,
        CollectionFactory $ticketSlaCollFactory,
        SlapolicyCollFactory $slapolicyCollFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_ticketsFactory = $ticketsFactory;
        $this->_eventsRepo = $eventsRepo;
        $this->_helpdeskLogger = $helpdeskLogger;
        $this->ticketslaRepo = $ticketslaRepo;
        $this->slaRepo = $slaRepo;
        $this->ticketSlaCollFactory = $ticketSlaCollFactory;
        $this->slapolicyCollFactory = $slapolicyCollFactory;
        $this->serializer = $serializer;
        $this->ticketHelper = $ticketHelper;
    }

    /**
     * Change property
     *
     * @return void
     */
    public function execute()
    {
        $wholedata = $this->getRequest()->getParams();
        $ticket = $this->_ticketsFactory->create()->load($wholedata["id"]);
        $from = "";
        if ($wholedata['field'] == "priority") {
            $from = $ticket->getPriority();

            $ticket->setPriority($wholedata['value']);
            $ticket->save();
            $this->checkSLACondition($ticket, $wholedata['field']);
            $this->_eventsRepo->checkTicketEvent("priority", $wholedata["id"], $from, $wholedata['value']);
        } elseif ($wholedata['field'] == "status") {
            $from = $ticket->getStatus();
            $ticket->setStatus($wholedata['value']);
            $ticket->save();
            $this->checkSLACondition($ticket, $wholedata['field']);
            $this->_eventsRepo->checkTicketEvent("status", $wholedata["id"], $from, $wholedata['value']);
        } elseif ($wholedata['field'] == "agent") {
            $from = $ticket->getToAgent();
            $ticket->setToAgent($wholedata['value']);
            $ticket->save();
            $this->checkSLACondition($ticket, $wholedata['field']);
            $this->_eventsRepo->checkTicketEvent("agent", $wholedata["id"], $from, $wholedata['value']);
        } elseif ($wholedata['field'] == "group") {
            $from = $ticket->getToGroup();
            $ticket->setToGroup($wholedata['value']);
            $ticket->save();
            $this->checkSLACondition($ticket, $wholedata['field']);
            $this->_eventsRepo->checkTicketEvent("group", $wholedata["id"], $from, $wholedata['value']);
        } elseif ($wholedata['field'] == "type") {
            $from = $ticket->getType();
            $ticket->setType($wholedata['value']);
            $ticket->save();
            $this->checkSLACondition($ticket, $wholedata['field']);
            $this->_eventsRepo->checkTicketEvent("type", $wholedata["id"], $from, $wholedata['value']);
        }
        $this->_eventsRepo->checkTicketEvent("ticket", $wholedata["id"], "updated");
        //$this->getResponse()->setHeader('Content-type', 'text/html');
        $resolveTime = $this->ticketHelper->getTicketResolveTime($ticket->getId());
        $responseTime = $this->ticketHelper->getTicketResponseTime($ticket->getId()) ?? [];
        $this->getResponse()->representJson(
            $this->serializer->serialize(["resolveTime" => $resolveTime, "responseTime" => $responseTime])
        );
        //$this->getResponse()->setBody(1);
        return $this->getResponse();
    }

    /**
     * CheckSLACondition
     *
     * @param object $ticket
     */
    protected function checkSLACondition($ticket, $actionType)
    {
        $collection = $this->slapolicyCollFactory->create()
                        ->setOrder("sort_order", "asc");
        $coll = $this->ticketSlaCollFactory->create()
                        ->addFieldToFilter('ticket_id', $ticket->getId());
        $finalFlag = false;
        foreach ($collection as $sla) {
            $oneCondition = $this->serializer->unserialize($sla->getOneConditionCheck());
            $allCondition = $this->serializer->unserialize($sla->getAllConditionCheck());
            $flag = false;
            if (isset($oneCondition['action-type'])) {
                $actionTypes = $oneCondition['action-type'];
                $flag = $this->slaRepo->checkOneCondition(
                                $oneCondition,
                                $flag,
                                $ticket
                            );
            }
            if (!$flag) {
                $actionTypes = $allCondition['action-type'];
                $flag = $this->slaRepo->checkCon($allCondition, $flag, $ticket);
            }
            if ($flag) {
                $finalFlag = true;
                if (!in_array($actionType, $actionTypes)) {
                    continue;
                }
                if ($coll->getSize()) {
                    $coll->walk('delete');
                }
                $this->ticketslaRepo->applySLAToTicket($ticket->getId(), $sla->getId());
                break;
            }
        }
        if (!$finalFlag && $coll->getSize()) {
            $coll->walk('delete');
        }
    }

    /**
     * Check Changeproperty Helpdesk Events Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Helpdesk::tickets');
    }
}
