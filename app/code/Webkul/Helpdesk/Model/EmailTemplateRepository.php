<?php
/**
 * Webkul Software.
 *
 * @category Webkul
 * @package  Webkul_Helpdesk
 * @author   Webkul
 * @license  https://store.webkul.com/license.html
 */
namespace Webkul\Helpdesk\Model;

use \Magento\Framework\Exception\CouldNotSaveException;

class EmailTemplateRepository implements \Webkul\Helpdesk\Api\EmailTemplateRepositoryInterface
{
    /**
     * @var \Magento\Framework\Session\SessionManager
     */
    protected $_sessionManager;

    /**
     * @var \Webkul\Helpdesk\Model\CustomerFactory
     */
    protected $_helpdeskCustomerFactory;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;
    protected $_ticketsFactory;
    protected $_threadFactory;
    protected $_tagFactory;
    protected $_groupFactory;
    protected $_ticketpriorityFactory;
    protected $_typeFactory;
    protected $_ticketstatusFactory;
    protected $_userFactory;
    protected $_custOrgFactory;
    protected $_helpdeskLogger;
    protected $_helper;

    /**
     * TicketsRepository constructor.
     *
     * @param \Magento\Framework\Session\SessionManager          $sessionManager
     * @param \Webkul\Helpdesk\Model\CustomerFactory             $helpdeskCustomerFactory
     * @param \Magento\Backend\Model\Auth\Session                $authSession
     * @param \Webkul\Helpdesk\Model\TicketsFactory              $ticketsFactory
     * @param \Webkul\Helpdesk\Model\ThreadFactory               $threadFactory
     * @param \Webkul\Helpdesk\Model\TagFactory                  $tagFactory
     * @param \Webkul\Helpdesk\Model\GroupFactory                $groupFactory
     * @param \Webkul\Helpdesk\Model\TicketsPriorityFactory      $ticketpriorityFactory
     * @param \Webkul\Helpdesk\Model\TypeFactory                 $typeFactory
     * @param \Webkul\Helpdesk\Model\TicketsStatusFactory        $ticketstatusFactory
     * @param \Magento\User\Model\UserFactory                    $userFactory
     * @param \Webkul\Helpdesk\Model\CustomerOrganizationFactory $custOrgFactory
     * @param \Webkul\Helpdesk\Logger\HelpdeskLogger             $helpdeskLogger
     * @param \Webkul\Helpdesk\Helper\Data                       $helper
     */
    public function __construct(
        \Magento\Framework\Session\SessionManager $sessionManager,
        \Webkul\Helpdesk\Model\CustomerFactory $helpdeskCustomerFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Webkul\Helpdesk\Model\TicketsFactory $ticketsFactory,
        \Webkul\Helpdesk\Model\ThreadFactory $threadFactory,
        \Webkul\Helpdesk\Model\TagFactory $tagFactory,
        \Webkul\Helpdesk\Model\GroupFactory $groupFactory,
        \Webkul\Helpdesk\Model\TicketsPriorityFactory $ticketpriorityFactory,
        \Webkul\Helpdesk\Model\TypeFactory $typeFactory,
        \Webkul\Helpdesk\Model\TicketsStatusFactory $ticketstatusFactory,
        \Magento\User\Model\UserFactory $userFactory,
        \Webkul\Helpdesk\Model\CustomerOrganizationFactory $custOrgFactory,
        \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger,
        \Webkul\Helpdesk\Helper\Data $helper
    ) {
        $this->_sessionManager = $sessionManager;
        $this->_helpdeskCustomerFactory = $helpdeskCustomerFactory;
        $this->_authSession = $authSession;
        $this->_ticketsFactory = $ticketsFactory;
        $this->_threadFactory = $threadFactory;
        $this->_tagFactory = $tagFactory;
        $this->_groupFactory = $groupFactory;
        $this->_ticketpriorityFactory = $ticketpriorityFactory;
        $this->_typeFactory = $typeFactory;
        $this->_ticketstatusFactory = $ticketstatusFactory;
        $this->_userFactory = $userFactory;
        $this->_custOrgFactory = $custOrgFactory;
        $this->_helpdeskLogger = $helpdeskLogger;
        $this->_helper = $helper;
    }

    /**
     * GetEmailCustomVariables This function returns the ticket custom variables for email
     *
     * @param  int $ticketId Ticket Id
     * @return array ticket custom variables
     */
    public function getEmailCustomVariables($ticketId)
    {
        try {
            $ticket = $this->_ticketsFactory->create()->load($ticketId);
            $thread = $this->_threadFactory->create()->getCollection()
                ->addFieldToFilter("ticket_id", ["eq"=>$ticketId])
                ->getLastItem();
            $variables = [];
            $variables['ticketid'] = "#".$ticket->getId();
            $variables['subject'] = $ticket->getSubject();
            $variables['query'] = $ticket->getQuery();
            $variables['source'] = $ticket->getSource();
            $variables['reply'] = $thread->getReply();
            $variables['tags'] = "";
            $tagname = [];
            $tagCollection = $this->_tagFactory->create()->getCollection();

            foreach ($tagCollection as $tag) {
                $ticketIds = $tag->getTicketIds() ? explode(",", $tag->getTicketIds()) : "";
                if ($ticketIds) {
                    if (in_array($ticketId, $ticketIds)) {
                        array_push($tagname, $tag->getName());
                    }
                }
            }

            $variables['tags'] = implode(',', $tagname);

            $data = $this->_sessionManager->getTicketReplyInfo();

            if (!empty($data) && ($data["type"] == "reply" || $data['type'] == "forward")) {

                $variables['reply'] = $thread->getReply();
                $threadCc = $thread->getCc() ? explode(',', $thread->getCc()) : [];
                $threadBcc = $thread->getBcc() ? explode(',', $thread->getBcc()) : [];

                $ticketCc = $ticket->getCc() ? explode(',', $ticket->getCc()) : [];
                $ticketBcc = $ticket->getBcc() ? explode(',', $ticket->getBcc()) : [];
                $variables['cc'] = implode(',', array_unique(array_filter(array_merge($threadCc, $ticketCc))));
                $variables['bcc'] = implode(',', array_unique(array_filter(array_merge($threadBcc, $ticketBcc))));
                $variables['to'] = $thread->getTo();
            } else {

                $ticketCc = $ticket->getCc() ? explode(',', $ticket->getCc()) : "";
                $ticketBcc = $ticket->getBcc() ? explode(',', $ticket->getBcc()): "";
                $variables['cc'] = $ticketCc ? implode(',', array_unique(array_filter($ticketCc))) : "";
                $variables['bcc'] = $ticketBcc ? implode(',', array_unique(array_filter($ticketBcc))) : "";
                $variables['to'] = "";
            }

            if ($ticket->getToGroup()) {
                $variables['group'] = $this->_groupFactory->create()->load($ticket->getToGroup())->getGroupname();
            }
            if ($ticket->getType()) {
                $variables['type'] = $this->_typeFactory->create()->load($ticket->getType())->getTypeName();
            }
            if ($ticket->getStatus()) {
                $variables['status'] = $this->_ticketstatusFactory->create()->load($ticket->getStatus())->getName();
            }
            if ($ticket->getPriority()) {
                $variables['priority'] = $this->_ticketpriorityFactory->create()->load(
                    $ticket->getPriority()
                )->getName();
            }
            $variables['agent_name'] = '';
            $variables['agent_email'] = '';

            if ($this->_helper->isAdmin()) {
                $agent = $this->_authSession->getUser();
                $variables['agent_name'] = $agent->getName();
                $variables['agent_email'] = $agent->getEmail();
            } else {
                if ($ticket->getToAgent()) {
                    $agent = $this->_userFactory->create()->load($ticket->getToAgent());
                    $variables['agent_name'] = $agent->getName();
                    $variables['agent_email'] = $agent->getEmail();
                }
            }

            if ($ticket->getCustomerId()) {
                $customer = $this->_helpdeskCustomerFactory->create()->load($ticket->getCustomerId());
                $variables['customer_name'] = $customer->getName();
                $variables['customer_email'] = $customer->getEmail();
                if ($customer->getOrganizations()) {
                    $organization = $this->_custOrgFactory->create()->load($customer->getOrganizations());
                    $variables['customer_organization'] = $organization->getName();
                }
            }
            return $variables;
        } catch (\Exception $e) {
            $this->_helpdeskLogger->info(__($e->getMessage()));
            throw new CouldNotSaveException(__('Unable to save ticket'), $e);
        }
    }

    /**
     * GetProcessedTemplate This function returns the process email template
     *
     * @param  HTML  $body               template html content
     * @param  array $emailTempVariables Email custom variables
     * @return HTML Email Content
     */
    public function getProcessedTemplate($body, $emailTempVariables)
    {
        foreach ($emailTempVariables as $var => $value) {
            $placeholder = "{{var ".$var."}}";
            $body = str_replace($placeholder, $value, $body);
        }
        return htmlspecialchars_decode($body);
    }

    /**
     * GetProcessedSubject This function returns the processed subject for email
     *
     * @param  string $text               Email subject
     * @param  array  $emailTempVariables Email custom variables
     * @return string $text Email processed subject
     */
    public function getProcessedSubject($text, $emailTempVariables)
    {
        foreach ($emailTempVariables as $var => $value) {
            $placeholder = "{{var ".$var."}}";
            $text = str_replace($placeholder, $value, $text);
        }
        return $text;
    }
}
