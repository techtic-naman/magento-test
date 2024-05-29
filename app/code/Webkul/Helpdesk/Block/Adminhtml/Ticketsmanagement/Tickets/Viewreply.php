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
namespace Webkul\Helpdesk\Block\Adminhtml\Ticketsmanagement\Tickets;

class Viewreply extends \Webkul\Helpdesk\Block\Adminhtml\Edit\Tab\AbstractAction
{
    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;

    /**
     * @var \Webkul\Helpdesk\Model\TicketsFactory
     */
    protected $_ticketsFactory;

    /**
     * @var \Webkul\Helpdesk\Model\TicketnotesFactory
     */
    protected $_ticketnotesFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute
     */
    protected $_eavAttribute;

    /**
     * @var \Webkul\Helpdesk\Model\ThreadFactory
     */
    protected $_threadFactory;

    /**
     * @var \Webkul\Helpdesk\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Webkul\Helpdesk\Helper\Tickets
     */
    protected $ticketHelper;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $filesystemFile;

    /**
     * @var \Webkul\Helpdesk\Model\TicketsCustomAttributesFactory
     */
    protected $_ticketsCusAttrFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context               $context
     * @param \Webkul\Helpdesk\Model\TicketsPriorityFactory         $ticketsPriorityFactory
     * @param \Webkul\Helpdesk\Model\TypeFactory                    $typeFactory
     * @param \Webkul\Helpdesk\Model\GroupFactory                   $groupFactory
     * @param \Webkul\Helpdesk\Model\AgentFactory                   $agentFactory
     * @param \Webkul\Helpdesk\Model\TicketsStatusFactory           $ticketsStatusFactory
     * @param \Webkul\Helpdesk\Model\EmailTemplateFactory           $emailTemplateFactory
     * @param \Webkul\Helpdesk\Model\TagFactory                     $tagFactory
     * @param \Webkul\Helpdesk\Model\ResponsesFactory               $responsesFactory
     * @param \Webkul\Helpdesk\Model\EventsFactory                  $eventsFactory
     * @param \Magento\Cms\Model\Wysiwyg\Config                     $wysiwygConfig
     * @param \Webkul\Helpdesk\Model\SlapolicyFactory               $slapolicyFactory
     * @param \Magento\User\Model\UserFactory                       $userFactory
     * @param \Webkul\Helpdesk\Model\TicketsFactory                 $ticketsFactory
     * @param \Webkul\Helpdesk\Model\TicketnotesFactory             $ticketnotesFactory
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute    $eavAttribute
     * @param \Webkul\Helpdesk\Model\ThreadFactory                  $threadFactory
     * @param \Magento\Framework\View\Asset\Repository              $assetRepo
     * @param \Webkul\Helpdesk\Helper\Data                          $dataHelper
     * @param \Webkul\Helpdesk\Helper\Tickets                       $ticketHelper
     * @param \Magento\Framework\Json\Helper\Data                   $jsonHelper
     * @param \Magento\Framework\Serialize\SerializerInterface      $serializer
     * @param \Magento\Framework\Filesystem\Driver\File             $filesystemFile
     * @param \Webkul\Helpdesk\Model\TicketsCustomAttributesFactory $ticketsCusAttrFactory
     * @param array                                                 $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Webkul\Helpdesk\Model\TicketsPriorityFactory $ticketsPriorityFactory,
        \Webkul\Helpdesk\Model\TypeFactory $typeFactory,
        \Webkul\Helpdesk\Model\GroupFactory $groupFactory,
        \Webkul\Helpdesk\Model\AgentFactory $agentFactory,
        \Webkul\Helpdesk\Model\TicketsStatusFactory $ticketsStatusFactory,
        \Webkul\Helpdesk\Model\EmailTemplateFactory $emailTemplateFactory,
        \Webkul\Helpdesk\Model\TagFactory $tagFactory,
        \Webkul\Helpdesk\Model\ResponsesFactory $responsesFactory,
        \Webkul\Helpdesk\Model\EventsFactory $eventsFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Webkul\Helpdesk\Model\SlapolicyFactory $slapolicyFactory,
        \Magento\User\Model\UserFactory $userFactory,
        \Webkul\Helpdesk\Model\TicketsFactory $ticketsFactory,
        \Webkul\Helpdesk\Model\TicketnotesFactory $ticketnotesFactory,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $eavAttribute,
        \Webkul\Helpdesk\Model\ThreadFactory $threadFactory,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Webkul\Helpdesk\Helper\Data $dataHelper,
        \Webkul\Helpdesk\Helper\Tickets $ticketHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\Framework\Filesystem\Driver\File $filesystemFile,
        \Webkul\Helpdesk\Model\TicketsCustomAttributesFactory $ticketsCusAttrFactory,
        array $data = []
    ) {
        $this->_ticketsFactory = $ticketsFactory;
        $this->_ticketnotesFactory = $ticketnotesFactory;
        $this->_eavAttribute = $eavAttribute;
        $this->_threadFactory = $threadFactory;
        $this->assetRepo = $assetRepo;
        $this->dataHelper = $dataHelper;
        $this->ticketHelper = $ticketHelper;
        $this->filesystemFile = $filesystemFile;
        $this->_ticketsCusAttrFactory = $ticketsCusAttrFactory;
        parent::__construct(
            $context,
            $ticketsPriorityFactory,
            $typeFactory,
            $groupFactory,
            $agentFactory,
            $ticketsStatusFactory,
            $emailTemplateFactory,
            $tagFactory,
            $responsesFactory,
            $eventsFactory,
            $wysiwygConfig,
            $slapolicyFactory,
            $userFactory,
            $dataHelper,
            $jsonHelper,
            $serializer,
            $data
        );
    }

    /**
     * Get current ticket data
     */
    public function getCurrentTicket()
    {
        $ticketId = $this->getRequest()->getParam('id');
        return $this->_ticketsFactory->create()->load($ticketId);
    }

    /**
     * Get note collection
     *
     * @param int $ticketId
     */
    public function getNoteCollection($ticketId)
    {
        return $this->_ticketnotesFactory->create()->getCollection()
            ->addFieldToFilter("ticket_id", ["eq"=>$ticketId]);
    }

    /**
     * Get attribute data
     *
     * @param int $attributeId
     */
    public function getAttributeData($attributeId)
    {
        return $this->_eavAttribute->load($attributeId);
    }

    /**
     * Return threads according to config limit setting
     *
     * @param int $threadlimit
     * @param int $ticketId
     * @return Object $collection Threads Collection
     */
    public function getCollection($threadlimit, $ticketId)
    {
        $collection = $this->_threadFactory->create()->getCollection()
            ->addFieldToFilter("ticket_id", $ticketId)
            ->addFieldToFilter("thread_type", ["neq"=>"create"]);
        $collection->setOrder('entity_id', 'DESC');
        $collection->setPageSize($threadlimit);
        $collection->setCurPage($this->getCurrentPage());
        return $collection;
    }

    /**
     * This function returns the current page count
     *
     * @return Int current page count
     */
    public function getCurrentPage()
    {
        $count = $this->getRequest()->getParam("currnetpage");
        if ($count) {
            return $count;
        } else {
            return 1;
        }
    }

    /**
     * Get pdf file url
     */
    public function pdfFileUrl()
    {
        return $this->_assetRepo->getUrl("Webkul_Helpdesk::images/pdf.png");
    }

    /**
     * Get doc file url
     */
    public function docFileUrl()
    {
        return $this->_assetRepo->getUrl("Webkul_Helpdesk::images/doc.png");
    }

    /**
     * Get helper data
     */
    public function helperData()
    {
        return $this->dataHelper;
    }

    /**
     * Get ticket helper data
     */
    public function ticketHelper()
    {
        return $this->ticketHelper;
    }
    /**
     * Check if file/folder exists
     *
     * @param string $file
     */
    public function fileExists($file)
    {
        return $this->filesystemFile->isExists($file);
    }

    /**
     * GetTicketUrl
     *
     * @param int $Id
     */
    public function getTicketUrl($Id)
    {
        $path = 'helpdesk/ticketsmanagement_tickets/viewreply';
        return $this->_urlBuilder->getUrl($path, ['id' => $Id]);
    }

    /**
     * GetCustomFieldAttributes
     *
     * @param int $type
     */
    public function getCustomFieldAttributes($type)
    {
        return $this->_ticketsCusAttrFactory->create()
            ->getAllowedTicketCustomerAttributes($type);
    }
}
