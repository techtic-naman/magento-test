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

class SlaRepository implements \Webkul\Helpdesk\Api\SlaRepositoryInterface
{
    /**
     * @var \Magento\Framework\Session\SessionManager
     */
    protected $_sessionManager;

    /**
     * @var \Webkul\Helpdesk\Model\EventsFactory
     */
    protected $_eventsFactory;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonHelper;

    /**
     * @var \Webkul\Helpdesk\Logger\HelpdeskLogger
     */
    protected $_helpdeskLogger;

    /**
     * @var \Webkul\Helpdesk\Model\TicketsFactory
     */
    protected $_ticketsFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var \Webkul\Helpdesk\Model\CustomerOrganizationFactory
     */
    protected $_custOrgFactory;

    /**
     * @var \Webkul\Helpdesk\Model\ResponsesRepository
     */
    protected $_responseRepo;

    /**
     * @var \Webkul\Helpdesk\Model\SlapolicyFactory
     */
    protected $_slapolicyFactory;

    /**
     * @var \Webkul\Helpdesk\Model\TicketslaRepository
     */
    protected $_ticketslaRepo;

    /**
     * @var \Webkul\Helpdesk\Helper\Tickets
     */
    protected $_ticketHelper;

    /**
     * TicketsRepository constructor.
     *
     * @param \Magento\Framework\Session\SessionManager          $sessionManager
     * @param \Webkul\Helpdesk\Model\SlapolicyFactory            $slapolicyFactory
     * @param \Magento\Framework\Json\Helper\Data                $jsonHelper
     * @param \Webkul\Helpdesk\Logger\HelpdeskLogger             $helpdeskLogger
     * @param \Webkul\Helpdesk\Model\TicketsFactory              $ticketsFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime        $date
     * @param \Webkul\Helpdesk\Model\CustomerOrganizationFactory $custOrgFactory
     * @param \Webkul\Helpdesk\Model\ResponsesRepository         $responseRepo
     * @param \Webkul\Helpdesk\Model\TicketslaRepository         $ticketslaRepo
     * @param \Webkul\Helpdesk\Helper\Tickets                    $ticketHelper
     */
    public function __construct(
        \Magento\Framework\Session\SessionManager $sessionManager,
        \Webkul\Helpdesk\Model\SlapolicyFactory $slapolicyFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger,
        \Webkul\Helpdesk\Model\TicketsFactory $ticketsFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Webkul\Helpdesk\Model\CustomerOrganizationFactory $custOrgFactory,
        \Webkul\Helpdesk\Model\ResponsesRepository $responseRepo,
        \Webkul\Helpdesk\Model\TicketslaRepository $ticketslaRepo,
        \Webkul\Helpdesk\Helper\Tickets $ticketHelper
    ) {
        $this->_sessionManager = $sessionManager;
        $this->_slapolicyFactory = $slapolicyFactory;
        $this->_jsonHelper = $jsonHelper;
        $this->_helpdeskLogger = $helpdeskLogger;
        $this->_ticketsFactory = $ticketsFactory;
        $this->_date = $date;
        $this->_custOrgFactory = $custOrgFactory;
        $this->_ticketslaRepo = $ticketslaRepo;
        $this->_ticketHelper = $ticketHelper;
    }

    /**
     * CreateThread Create thread function
     *
     * @param int    $ticketId Ticket Id
     * @param string $type     Reply type
     */
    public function checkTicketConditionForSla($ticketId, $type = null)
    {
        try {
            $ticket = $this->_ticketsFactory->create()->load($ticketId);
            $collection = $this->_slapolicyFactory->create()
                ->getCollection()
                ->setOrder("sort_order", "asc");
            foreach ($collection as $sla) {
                $oneCondition = json_decode($sla->getOneConditionCheck(), true);
                $flag = false;
                $flag = $this->checkOneCondition($oneCondition, $flag, $ticket);
                if ($flag) {
                    if ($type == "reply") {
                        $this->_ticketslaRepo->applyReplySLAToTicket(
                            $ticketId,
                            $sla->getId()
                        );
                    } else {
                        $this->_ticketslaRepo->applySLAToTicket(
                            $ticketId,
                            $sla->getId()
                        );
                    }
                } else {
                    $this->checkFlag($sla, $flag, $ticket);
                }   
            }
        } catch (\Exception $e) {
            $this->_helpdeskLogger->info($e->getMessage());
            throw new CouldNotSaveException(__('There are some error in SLA'), $e);
        }
    }

    /**
     * CheckCondition Rules for condition check
     *
     * @param  string $condition Condition
     * @param  string $haystack  Haystack
     * @param  string $needle    Needle
     * @return boolean
     */
    public function checkCondition($condition, $haystack, $needle)
    {
        switch ($condition) {
            case 'is':
                if ($haystack == $needle) {
                    return true;
                } else {
                    return false;
                }
                break;
            case 'isNot':
                if ($haystack == $needle) {
                    return false;
                } else {
                    return true;
                }
                break;
            case 'isContains':
                if (strpos($haystack, $needle) !== false) {
                    return true;
                } else {
                    return false;
                }
                break;
            case 'isNotContains':
                if (strpos($haystack, $needle) !== false) {
                    return false;
                } else {
                    return true;
                }
                break;
            case 'startWith':
                return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
            case 'endWith':
                return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0
                && strpos($haystack, $needle, $temp) !== false);
            case 'before':
                $createdTimeStamp = strtotime($haystack);
                $conditionTimeStamp = strtotime($needle." 23:59:59");
                if ($createdTimeStamp < $conditionTimeStamp) {
                    return true;
                } else {
                    return false;
                }
                break;
            case 'beforeOn':
                $createdTimeStamp = strtotime($haystack);
                $conditionTimeStamp = strtotime($needle." 23:59:59");
                if ($createdTimeStamp < $conditionTimeStamp || $createdTimeStamp == $conditionTimeStamp) {
                    return true;
                } else {
                    return false;
                }
                break;
            case 'after':
                $createdTimeStamp = strtotime($haystack);
                $conditionTimeStamp = strtotime($needle." 23:59:59");
                if ($createdTimeStamp > $conditionTimeStamp) {
                    return true;
                } else {
                    return false;
                }
                break;
            case 'afterOn':
                $createdTimeStamp = strtotime($haystack);
                $conditionTimeStamp = strtotime($needle." 23:59:59");
                if ($createdTimeStamp > $conditionTimeStamp || $createdTimeStamp == $conditionTimeStamp) {
                    return true;
                } else {
                    return false;
                }
                break;
        }
    }

    /**
     * CheckCondition
     *
     * @param  string $oneCondition
     * @param  string $flag
     * @param  object $ticket
     * @return boolean
     */
    public function checkOneCondition($oneCondition, $flag, $ticket)
    {
        if (isset($oneCondition['action-type'])) {
            foreach ($oneCondition['action-type'] as $actionType) {
                switch ($actionType) {
                    case 'from':
                        $flag = $this->checkCondition(
                            $oneCondition['from']
                            ['match_condition'],
                            $ticket->getFrom(),
                            $oneCondition['from']
                            ['match']
                        );
                        break;
                    case 'to':
                        $flag = $this->checkCondition(
                            $oneCondition['to']
                            ['match_condition'],
                            $ticket->getTo(),
                            $oneCondition['to']
                            ['match']
                        );
                        break;
                    case 'subject':
                        $flag = $this->checkCondition(
                            $oneCondition['subject']
                            ['match_condition'],
                            $ticket->getSubject(),
                            $oneCondition
                            ['subject']['match']
                        );
                        break;
                    case 'description':
                        $flag = $this->checkCondition(
                            $oneCondition['description']
                            ['match_condition'],
                            $ticket->getSubject(),
                            $oneCondition
                            ['description']['match']
                        );
                        break;
                    case 'subject_description':
                        $flag = $this->checkCondition(
                            $oneCondition
                            ['subject_description']['match_condition'],
                            $ticket->getSubject(),
                            $oneCondition['subject_description']
                            ['match']
                        );
                        break;
                    case 'priority':
                        $flag = $this->checkCondition(
                            $oneCondition['priority']
                            ['match_condition'],
                            $ticket->getPriority(),
                            $oneCondition
                            ['priority']['match']
                        );
                        break;
                    case 'status':
                        $flag = $this->checkCondition(
                            $oneCondition['status']
                            ['match_condition'],
                            $ticket->getPriority(),
                            $oneCondition
                            ['status']['match']
                        );
                        break;
                    case 'source':
                        $flag = $this->checkCondition(
                            $oneCondition['source']
                            ['match_condition'],
                            $ticket->getSource(),
                            $oneCondition
                            ['source']['match']
                        );
                        break;
                    case 'created':
                        $flag = $this->checkCondition(
                            $oneCondition['created']
                            ['match_condition'],
                            $ticket->getCreatedAt(),
                            $oneCondition
                            ['created']['match']
                        );
                        break;
                    case 'agent':
                        $flag = $this->checkCondition(
                            $oneCondition['agent']
                            ['match_condition'],
                            $ticket->getToAgent(),
                            $oneCondition
                            ['agent']['match']
                        );
                        break;
                    case 'group':
                        $flag = $this->checkCondition(
                            $oneCondition['group']['match_condition'],
                            $ticket->getToGroup(),
                            $oneCondition['group']['match']
                        );
                        break;
                    case 'customer_email':
                        $flag = $this->checkCondition(
                            $oneCondition['customer_email']
                            ['match_condition'],
                            $ticket->getEmail(),
                            $oneCondition
                            ['customer_email']['match']
                        );
                        break;
                    case 'customer_name':
                        $flag = $this->checkCondition(
                            $oneCondition['customer_name']
                            ['match_condition'],
                            $ticket->getFullname(),
                            $oneCondition
                            ['customer_name']['match']
                        );
                        break;
                    case 'organization_name':
                        $orgName = "";
                        $helpdeskCustomer =
                        $this->_ticketHelper->getHelpdeskCustomerById($ticket->getCustomerId());
                        if ($helpdeskCustomer->getOrganizations()!="") {
                            $org = $this->_custOrgFactory->create()->load(
                                $helpdeskCustomer->getOrganizations()
                            );
                            $orgName = $org->getName();
                        }

                        if ($orgName!="") {
                            $flag = $this->checkCondition(
                                $oneCondition
                                ['organization_name']['match_condition'],
                                $orgName,
                                $oneCondition['organization_name']['match']
                            );
                        }
                        break;
                    case 'organization_domain':
                        $orgDomain = "";
                        $helpdeskCustomer =
                        $this->_ticketHelper->getHelpdeskCustomerById($ticket->getCustomerId());
                        if ($helpdeskCustomer->getOrganizations()!="") {
                            $org = $this->_custOrgFactory->create()->load(
                                $helpdeskCustomer->getOrganizations()
                            );
                            $orgDomain = $org->getDomain();
                        }
                        if ($orgDomain!="") {
                            $flag = $this->checkCondition(
                                $oneCondition
                                ['organization_domain']['match_condition'],
                                $orgDomain,
                                $oneCondition['organization_domain']['match']
                            );
                        }
                        break;
                }
                if ($flag) {
                    return $flag;     
                }
            }
        }
    }

    /**
     * Checkflag
     *
     * @param  object $sla
     * @param  string $flag
     * @param  object $ticket
     * @param  string|null $type
     */
    public function checkFlag($sla, $flag, $ticket, $type = null)
    {
        if (!$flag) {
            $allCondition = json_decode($sla->getAllConditionCheck(), true);
            $flag = false;
            $flag = $this->checkCon($allCondition, $flag, $ticket);
            if ($flag) {
                $ticketId = $ticket->getId();
                if ($type == "reply") {
                    $this->_ticketslaRepo->applyReplySLAToTicket(
                        $ticketId,
                        $sla->getId()
                    );
                } else {
                    $this->_ticketslaRepo->applySLAToTicket($ticketId, $sla->getId());
                }
            }
        }
    }

    /**
     * Check condition for flag
     *
     * @param  array $allCondition
     * @param  string $flag
     * @param  object $ticket
     */
    public function checkCon($allCondition, $flag, $ticket)
    {
        if (isset($allCondition['action-type'])) {
            foreach ($allCondition['action-type'] as $actionType) {

                switch ($actionType) {
                    case 'from':
                        $flag = $this->checkCondition(
                            $allCondition['from']
                            ['match_condition'],
                            $ticket->getFrom(),
                            $allCondition
                            ['from']['match']
                        );
                        break;
                    case 'to':
                        $flag = $this->checkCondition(
                            $allCondition['to']
                            ['match_condition'],
                            $ticket->getTo(),
                            $allCondition['to']
                            ['match']
                        );
                        break;
                    case 'subject':
                        $flag = $this->checkCondition(
                            $allCondition['subject']
                            ['match_condition'],
                            $ticket->getSubject(),
                            $allCondition
                            ['subject']['match']
                        );
                        break;
                    case 'description':
                        $flag = $this->checkCondition(
                            $allCondition['description']
                            ['match_condition'],
                            $ticket->getSubject(),
                            $allCondition
                            ['description']['match']
                        );
                        break;
                    case 'subject_description':
                        $flag = $this->checkCondition(
                            $allCondition
                            ['subject_description']['match_condition'],
                            $ticket->getSubject(),
                            $allCondition
                            ['subject_description']['match']
                        );
                        break;
                    case 'priority':
                        $flag = $this->checkCondition(
                            $allCondition['priority']
                            ['match_condition'],
                            $ticket->getPriority(),
                            $allCondition
                            ['priority']['match']
                        );
                        break;
                    case 'status':
                        $flag = $this->checkCondition(
                            $allCondition['status']
                            ['match_condition'],
                            $ticket->getStatus(),
                            $allCondition
                            ['status']['match']
                        );
                        break;
                    case 'source':
                        $flag = $this->checkCondition(
                            $allCondition['source']
                            ['match_condition'],
                            $ticket->getSource(),
                            $allCondition
                            ['source']['match']
                        );
                        break;
                    case 'created':
                        $flag = $this->checkCondition(
                            $allCondition['created']
                            ['match_condition']." 23:59:59",
                            $ticket->getCreatedAt(),
                            $allCondition['created']['match']
                        );
                        break;
                    case 'agent':
                        $flag = $this->checkCondition(
                            $allCondition['agent']
                            ['match_condition'],
                            $ticket->getToAgent(),
                            $allCondition
                            ['agent']['match']
                        );
                        break;
                    case 'group':
                        $flag = $this->checkCondition(
                            $allCondition['group']
                            ['match_condition'],
                            $ticket->getToGroup(),
                            $allCondition
                            ['group']['match']
                        );
                        break;
                    case 'customer_email':
                        $flag = $this->checkCondition(
                            $allCondition
                            ['customer_email']['match_condition'],
                            $ticket->getEmail(),
                            $allCondition['customer_email']['match']
                        );
                        break;
                    case 'customer_name':
                        $flag = $this->checkCondition(
                            $allCondition
                            ['customer_name']['match_condition'],
                            $ticket->getFullname(
                            ),
                            $allCondition['customer_name']['match']
                        );
                        break;
                    case 'organization_name':
                        $orgName = "";
                        $helpdeskCustomer =
                        $this->_ticketHelper->getHelpdeskCustomerById($ticket->getCustomerId());
                        if ($helpdeskCustomer->getOrganizations()!="") {
                            $org = $this->_custOrgFactory->create()->load(
                                $helpdeskCustomer->getOrganizations()
                            );
                            $orgName = $org->getName();
                        }

                        if ($orgName!="") {
                            $flag = $this->checkCondition(
                                $allCondition
                                ['organization_name']['match_condition'],
                                $orgName,
                                $allCondition['organization_name']['match']
                            );
                        }
                        break;
                    case 'organization_domain':
                        $orgDomain = "";
                        $helpdeskCustomer =
                        $this->_ticketHelper->getHelpdeskCustomerById(
                            $ticket->getCustomerId()
                        );
                        if ($helpdeskCustomer->getOrganizations()!="") {
                            $org = $this->_custOrgFactory->create()->load(
                                $helpdeskCustomer->getOrganizations()
                            );
                            $orgDomain = $org->getDomain();
                        }
                        if ($orgDomain!="") {
                            $flag = $this->checkCondition(
                                $allCondition
                                ['organization_domain']['match_condition'],
                                $orgDomain,
                                $allCondition['organization_domain']
                                ['match']
                            );
                        }
                        break;
                }
                if (!$flag) {
                    break;
                }
            }
            return $flag;
        }
    }
}
