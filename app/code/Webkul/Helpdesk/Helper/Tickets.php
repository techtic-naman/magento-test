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

namespace Webkul\Helpdesk\Helper;

use Magento\Framework\Encryption\EncryptorInterface as Encryptor;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Webkul Helpdesk Helper Data.
 */
class Tickets extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * @var \Magento\Customer\Model\Context
     */
    protected $_customAttribute;

    /**
     * @var \Webkul\Helpdesk\Model\TicketsFactory
     */
    protected $_ticketsFactory;

    /**
     * @var \Webkul\Helpdesk\Model\ThreadFactory
     */
    protected $_threadFactory;

    /**
     * @var \Webkul\Helpdesk\Model\TicketsPriorityFactory
     */
    protected $_priorityFactory;

    /**
     * @var \Webkul\Helpdesk\Model\TicketsStatusFactory
     */
    protected $_ticketsStatusFactory;

    /**
     * @var \Webkul\Helpdesk\Model\TypeFactory
     */
    protected $_typeFactory;

    /**
     * @var \Webkul\Helpdesk\Model\TicketslaRepository
     */
    protected $_ticketslaRepo;

    /**
     * @var \Webkul\Helpdesk\Model\AgentFactory
     */
    protected $_agentFactory;

    /**
     * @var \Webkul\Helpdesk\Model\GroupFactory
     */
    protected $_groupFactory;

    /**
     * @var \Webkul\Helpdesk\Model\TicketsAttributeValueFactory
     */
    protected $_ticketsAttrValueFactory;

    /**
     * @var \Webkul\Helpdesk\Model\MailfetchFactory
     */
    protected $_mailfetchFactory;

    /**
     * @var \Webkul\Helpdesk\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Webkul\Helpdesk\Model\TicketdraftFactory
     */
    protected $_ticketdraftFactory;

    /**
     * @var \Webkul\Helpdesk\Model\ResponsesFactory
     */
    protected $_responsesFactory;

    /**
     * @var \Magento\User\Model\UserFactory
     */
    protected $_userFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_mageCustomerSession;

    /**
     * @var \Magento\Framework\Session\SessionManager
     */
    protected $_coreSession;

    /**
     * @var \Webkul\Helpdesk\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Webkul\Helpdesk\Model\ResourceModel\CustomerFactory
     */
    protected $_resCustFactory;

    /**
     * @var Encryptor
     */
    protected $encryptor;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @param \Magento\Framework\App\Helper\Context                $context
     * @param \Magento\Backend\Model\Auth\Session                  $authSession
     * @param \Webkul\Helpdesk\Model\TicketsFactory                $ticketsFactory
     * @param \Webkul\Helpdesk\Model\ThreadFactory                 $threadFactory
     * @param \Webkul\Helpdesk\Model\TicketsPriorityFactory        $priorityFactory
     * @param \Webkul\Helpdesk\Model\TicketsStatusFactory          $ticketsStatusFactory
     * @param \Webkul\Helpdesk\Model\TypeFactory                   $typeFactory
     * @param \Webkul\Helpdesk\Model\TicketslaRepository           $ticketslaRepo
     * @param \Webkul\Helpdesk\Model\AgentFactory                  $agentFactory
     * @param \Webkul\Helpdesk\Model\GroupFactory                  $groupFactory
     * @param \Webkul\Helpdesk\Model\TicketsAttributeValueFactory  $ticketsAttrValueFactory
     * @param \Webkul\Helpdesk\Model\MailfetchFactory              $mailfetchFactory
     * @param \Webkul\Helpdesk\Model\CustomerFactory               $customerFactory
     * @param \Webkul\Helpdesk\Model\TicketdraftFactory            $ticketdraftFactory
     * @param \Webkul\Helpdesk\Model\ResponsesFactory              $responsesFactory
     * @param \Magento\User\Model\UserFactory                      $userFactory
     * @param \Magento\Customer\Model\Session                      $mageCustomerSession
     * @param \Magento\Framework\Session\SessionManager            $coreSession
     * @param \Webkul\Helpdesk\Helper\Data                         $helper
     * @param \Webkul\Helpdesk\Model\Eav\CustomAttributeFactory    $customAttribute
     * @param \Webkul\Helpdesk\Model\ResourceModel\CustomerFactory $resCustFactory
     * @param Encryptor                                            $encryptor
     * @param SerializerInterface                                  $serializer
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Webkul\Helpdesk\Model\TicketsFactory $ticketsFactory,
        \Webkul\Helpdesk\Model\ThreadFactory $threadFactory,
        \Webkul\Helpdesk\Model\TicketsPriorityFactory $priorityFactory,
        \Webkul\Helpdesk\Model\TicketsStatusFactory $ticketsStatusFactory,
        \Webkul\Helpdesk\Model\TypeFactory $typeFactory,
        \Webkul\Helpdesk\Model\TicketslaRepository $ticketslaRepo,
        \Webkul\Helpdesk\Model\AgentFactory $agentFactory,
        \Webkul\Helpdesk\Model\GroupFactory $groupFactory,
        \Webkul\Helpdesk\Model\TicketsAttributeValueFactory $ticketsAttrValueFactory,
        \Webkul\Helpdesk\Model\MailfetchFactory $mailfetchFactory,
        \Webkul\Helpdesk\Model\CustomerFactory $customerFactory,
        \Webkul\Helpdesk\Model\TicketdraftFactory $ticketdraftFactory,
        \Webkul\Helpdesk\Model\ResponsesFactory $responsesFactory,
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Customer\Model\Session $mageCustomerSession,
        \Magento\Framework\Session\SessionManager $coreSession,
        \Webkul\Helpdesk\Helper\Data $helper,
        \Webkul\Helpdesk\Model\Eav\CustomAttributeFactory $customAttribute,
        \Webkul\Helpdesk\Model\ResourceModel\CustomerFactory $resCustFactory,
        Encryptor $encryptor,
        SerializerInterface $serializer
    ) {
        parent::__construct($context);
        $this->_authSession = $authSession;
        $this->_ticketsFactory = $ticketsFactory;
        $this->_threadFactory = $threadFactory;
        $this->_priorityFactory = $priorityFactory;
        $this->_ticketsStatusFactory = $ticketsStatusFactory;
        $this->_typeFactory = $typeFactory;
        $this->_ticketslaRepo = $ticketslaRepo;
        $this->_agentFactory = $agentFactory;
        $this->_groupFactory = $groupFactory;
        $this->_ticketsAttrValueFactory = $ticketsAttrValueFactory;
        $this->_mailfetchFactory = $mailfetchFactory;
        $this->_customerFactory = $customerFactory;
        $this->_ticketdraftFactory = $ticketdraftFactory;
        $this->_responsesFactory = $responsesFactory;
        $this->_userFactory = $userFactory;
        $this->_mageCustomerSession = $mageCustomerSession;
        $this->_coreSession = $coreSession;
        $this->_helper = $helper;
        $this->_customAttribute = $customAttribute;
        $this->_resCustFactory = $resCustFactory;
        $this->encryptor = $encryptor;
        $this->serializer = $serializer;
    }

    /**
     * This function returns total thread count
     *
     * @param int $ticketId
     * @return String total thread count
     */
    public function getTotalThreads($ticketId)
    {
        $collection = $this->_threadFactory->create()->getCollection()
            ->addFieldToFilter("ticket_id", $ticketId)
            ->addFieldToFilter("thread_type", ["neq"=>"create"]);
        return count($collection);
    }

    /**
     * This function returns current user id
     *
     * @return int
     */
    public function getCurrentAgentId()
    {
        return $this->_authSession->getUser()->getId();
    }

    /**
     * Get Current ticket priority name
     *
     * @param int $ticketId
     * @return string
     */
    public function getTicketPriorityName($ticketId)
    {
        $priorityName = "";
        $ticket = $this->_ticketsFactory->create()->load($ticketId);
        $priorityArr = $this->_priorityFactory->create()->toOptionArray();
        foreach ($priorityArr as $priority) {
            if ($ticket->getPriority()==$priority['value']) {
                $priorityName = $priority['label'];
            }
        }
        return $priorityName;
    }

    /**
     * Get Current ticket status name
     *
     * @param int $ticketId
     * @return string
     */
    public function getTicketStatusName($ticketId)
    {
        $statusName = "";
        $ticket = $this->_ticketsFactory->create()->load($ticketId);
        $statusArr = $this->_ticketsStatusFactory->create()->toOptionArray();
        foreach ($statusArr as $status) {
            if ($ticket->getStatus()==$status['value']) {
                $statusName = $status['label'];
            }
        }
        return $statusName;
    }

    /**
     * Get Current ticket type name
     *
     * @param int $ticketId
     * @return string
     */
    public function getTicketTypeName($ticketId)
    {
        $typeName = "";
        $ticket = $this->_ticketsFactory->create()->load($ticketId);
        $typeArr = $this->_typeFactory->create()->toOptionArray();
        foreach ($typeArr as $type) {
            if ($ticket->getType()==$type['value']) {
                $typeName = $type['label'];
            }
        }
        return $typeName;
    }

    /**
     * Return agent timesatmp offset
     *
     * @return Float $offset Agent timesatmp offset
     */
    public function getAgentOffset()
    {
        // $offset = "";
        /**
         * Fix: If offset is not it will throw error non-numeric value on viewreply.phtml previously
         */
        $offset = 0;
        $userId = $this->getCurrentAgentId();
        $agent = $this->_agentFactory->create()->getCollection()
            ->addFieldToFilter("user_id", ["eq" => $userId])
            ->getFirstItem();
        if ($agent->getTimezone()) {
            $offset = timezone_offset_get(timezone_open($agent->getTimezone()), new \DateTime());
        }
        return $offset;
    }

    /**
     * Get ticket response time
     *
     * @param int $ticketId
     * @return String
     */
    public function getTicketResponseTime($ticketId)
    {
        return $this->_ticketslaRepo->getTicketResponseTime($ticketId);
    }

    /**
     * Get ticket resolve time
     *
     * @param int $ticketId
     * @return String
     */
    public function getTicketResolveTime($ticketId)
    {
        return $this->_ticketslaRepo->getTicketResolveTime($ticketId);
    }
    
    /**
     * Get ticket agent name
     *
     * @param int $ticketId
     * @return String
     */
    public function getTicketAgentName($ticketId)
    {
        $agentName = "";
        $ticket = $this->_ticketsFactory->create()->load($ticketId);
        $agentArr = $this->_agentFactory->create()->toOptionArray();
        foreach ($agentArr as $agent) {
            if ($ticket->getToAgent()==$agent['value']) {
                $agentName = $agent['label'];
            }
        }
        return $agentName;
    }

    /**
     * Get ticket group name
     *
     * @param int $ticketId
     * @return String
     */
    public function getTicketGroupName($ticketId)
    {
        $groupName = "";
        $ticket = $this->_ticketsFactory->create()->load($ticketId);
        $groupArr = $this->_groupFactory->create()->toOptionArray();
        foreach ($groupArr as $group) {
            if ($ticket->getToGroup()==$group['value']) {
                $groupName = $group['label'];
            }
        }
        return $groupName;
    }

    /**
     * Retun ticket attribute details
     *
     * @param int $ticketId
     * @return Object Ticket attribute details
     */
    public function getTicketAttributeDetails($ticketId)
    {
        return $this->_ticketsAttrValueFactory->create()->getCollection()
            ->addFieldToFilter("ticket_id", ["eq"=>$ticketId]);
    }

    /**
     * This function returns create type thread
     *
     * @param int $ticketId
     * @return Object
     */
    public function getCreateTypeTreadDetails($ticketId)
    {
        $collection = $this->_threadFactory->create()->getCollection()
            ->addFieldToFilter("ticket_id", $ticketId);
        return $collection->getFirstItem();
    }

    /**
     * This function returns the max pages count
     *
     * @param int $threadLimit
     * @param int $ticketId
     * @return int
     */
    public function getMaxPages($threadLimit, $ticketId)
    {
        $collection = $this->_threadFactory->create()->getCollection()
            ->addFieldToFilter("ticket_id", $ticketId)
            ->addFieldToFilter("thread_type", ["neq"=>"create"])
            ->setCurPage(1)->setPageSize($threadLimit);
        return $collection->getLastPageNumber();
    }

    /**
     * This function returns mails
     *
     * @param int $threadId
     * @return Object
     */
    public function getMailFetchCollection($threadId)
    {
        return $this->_mailfetchFactory->create()->getCollection()
            ->addFieldToFilter("thread_id", $threadId)
            ->getFirstItem();
    }

    /**
     * This function returns loggin admin user
     *
     * @return Object
     */
    public function getCurrentAgent()
    {
        return $this->_authSession->getUser();
    }

    /**
     * This function returns agent draft content
     *
     * @param int $fieldType
     * @param int $ticketId
     * @return String
     */
    public function getDraftContent($fieldType, $ticketId)
    {
        if ($this->_helper->isAdmin()) {
            $userId = $this->getCurrentAgentId();
            $usertype = "admin";
        } else {
            $userId = $this->getTsCustomerId();
            $usertype = "customer";
        }

        $ticketCollection = $this->_ticketdraftFactory->create()->getCollection()
            ->addFieldToFilter("ticket_id", ["eq"=>$ticketId])
            ->addFieldToFilter("user_id", ["eq"=>$userId])
            ->addFieldToFilter("user_type", ["eq"=>$usertype])
            ->addFieldToFilter("field", ["eq"=>$fieldType]);
        $content = $ticketCollection->getFirstItem()->getContent();
        if ($usertype=="admin") {
            if ($content == "") {
                if ($fieldType == "reply" || $fieldType == "forward") {
                    $agent = $this->_agentFactory->create()->getCollection()
                        ->addFieldToFilter("user_id", $userId)
                        ->getFirstItem();
                    if ($agent->getSignature() != "") {
                        $content = "<br/><br/><br/><br/>".nl2br($agent->getSignature());
                    }
                }
            }
        }
        return $content;
    }

    /**
     * GetDraftContentForNewForm
     */
    public function getDraftContentForNewForm()
    {
        $content = $this->getDraftContent('new', 0);
        if ($content) {
            $content = $this->serializer->unserialize($content);
        }
        return $content ?? [];
    }

    /**
     * This function returns responses
     *
     * @return object
     */
    public function getAllEnableResponses()
    {
        return $this->_responsesFactory->create()->getCollection()
            ->addFieldToFilter("status", ["eq"=>1]);
    }

    /**
     * This function returns group
     *
     * @param int $groupId
     * @return object
     */
    public function getGroupDataById($groupId)
    {
        return $this->_groupFactory->create()->load($groupId);
    }

    /**
     * This function returns helpdesk Customer
     *
     * @param int $customerId
     * @return object
     */
    public function getHelpdeskCustomerById($customerId)
    {
        return $this->_customerFactory->create()->load($customerId);
    }

    /**
     * This function returns helpdesk Customer
     *
     * @param int $mageCustomerId
     * @return object
     */
    public function getHelpdeskCustomerByMageCustomerId($mageCustomerId)
    {
        return $this->_customerFactory->create()->getCollection()
            ->addFieldToFilter("customer_id", ["eq"=>$mageCustomerId])
            ->getFirstItem();
    }

     /**
      * This function returns helpdesk Customer
      *
      * @param $string $email
      * @return object customer model
      */
    public function getHelpdeskCustomerByMageCustomerEmail($email)
    {
        return $this->_customerFactory->create()->getCollection()
            ->addFieldToFilter("email", ["eq"=>$email])
            ->getFirstItem();
    }

    /**
     * This function return admin user data
     *
     * @param int $userId
     * @return object customer model
     */
    public function getAdminUserById($userId)
    {
        return $this->_userFactory->create()->load($userId);
    }

    /**
     * Get customer session
     *
     * @return object
     */
    public function getCustomerSession()
    {
        return $this->_mageCustomerSession;
    }

    /**
     * Returns logged in customer id
     *
     * @param bool $isSubmit
     * @return int
     */
    public function getTsCustomerId($isSubmit = false)
    {
        $userId = 0;
        if ($this->_mageCustomerSession->isLoggedIn()) {
            $userId = $this->_mageCustomerSession->getCustomerId();
            $customerData = $this->_mageCustomerSession->getCustomerData();
            $customer = $this->_customerFactory->create()->getCollection()
                ->addFieldToFilter("customer_id", $userId);
            if (!$customer->getSize() && $isSubmit) {
                $helpdeskCust = $this->_customerFactory->create();
                $helpdeskCust->setCustomerId($userId);
                $name = $customerData->getFirstName()." ".$customerData->getLastName();
                $helpdeskCust->setName($name);
                $helpdeskCust->setEmail($customerData->getEmail());
                $this->_resCustFactory->create()->save($helpdeskCust);
                $userId = $helpdeskCust->getId();
            } else {
                $userId = $customer->getFirstItem()->getId();
            }
        } else {
            $data = $this->_coreSession->getTsCustomer();
            $userId = $data['customer_id'] ?? 0;
        }
        return $userId;
    }

    /**
     * Returns guest customer data
     *
     * @return Array
     */
    public function getTsCustomerData()
    {
        return $this->_coreSession->getTsCustomer();
    }

    /**
     * Unset customer session
     */
    public function unSetTsCustomerData()
    {
        $this->_coreSession->unsTsCustomer();
    }

    /**
     * Returns encrypted data
     *
     * @param string $data
     * @return string
     */
    public function encryptData($data)
    {
        return $this->encryptor->encrypt($data);
    }

    /**
     * Returns decrypted data
     *
     * @param string $data
     * @return string
     */
    public function decryptData($data)
    {
        return $this->encryptor->decrypt($data);
    }
}
