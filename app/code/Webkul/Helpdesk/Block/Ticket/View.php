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

namespace Webkul\Helpdesk\Block\Ticket;

class View extends Navigation
{

    /**
     * @var \Webkul\Helpdesk\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Webkul\Helpdesk\Helper\Tickets
     */
    protected $_ticketsHelper;

    /**
     * @var \Webkul\Helpdesk\Model\TicketsFactory
     */
    protected $_ticketsFactory;

    /**
     * @var \Webkul\Helpdesk\Model\ThreadFactory
     */
    protected $_threadFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute
     */
    protected $_eavAttribute;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $filesystemFile;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context      $context
     * @param \Webkul\Helpdesk\Model\SupportCenterFactory           $supportcenterFactory
     * @param \Magento\Cms\Model\PageFactory                        $cmspageFactory
     * @param \Webkul\Helpdesk\Model\TypeFactory                    $typeFactory
     * @param \Webkul\Helpdesk\Model\TicketsPriorityFactory         $ticketsPriorityFactory
     * @param \Webkul\Helpdesk\Model\GroupFactory                   $groupFactory
     * @param \Webkul\Helpdesk\Model\TicketsStatusFactory           $ticketsStatusFactory
     * @param \Webkul\Helpdesk\Model\TicketsCustomAttributesFactory $ticketsCusAttrFactory
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute    $eavAttribute
     * @param \Webkul\Helpdesk\Helper\Tickets                       $ticketsHelper
     * @param \Webkul\Helpdesk\Helper\Data                          $dataHelper
     * @param \Webkul\Helpdesk\Model\TicketsFactory                 $ticketsFactory
     * @param \Webkul\Helpdesk\Model\ThreadFactory                  $threadFactory
     * @param \Magento\Framework\View\Asset\Repository              $assetRepo
     * @param \Magento\Framework\Json\Helper\Data                   $jsonHelper
     * @param \Magento\Framework\Filesystem\Driver\File             $filesystemFile
     * @param \Magento\Framework\App\ResourceConnection             $resource
     * @param \Magento\Framework\Escaper                            $escaper
     * @param \Magento\Eav\Model\Entity\Attribute\Source\Table      $sorceTable
     * @param array                                                 $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Webkul\Helpdesk\Model\SupportCenterFactory $supportcenterFactory,
        \Magento\Cms\Model\PageFactory $cmspageFactory,
        \Webkul\Helpdesk\Model\TypeFactory $typeFactory,
        \Webkul\Helpdesk\Model\TicketsPriorityFactory $ticketsPriorityFactory,
        \Webkul\Helpdesk\Model\GroupFactory $groupFactory,
        \Webkul\Helpdesk\Model\TicketsStatusFactory $ticketsStatusFactory,
        \Webkul\Helpdesk\Model\TicketsCustomAttributesFactory $ticketsCusAttrFactory,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $eavAttribute,
        \Webkul\Helpdesk\Helper\Tickets $ticketsHelper,
        \Webkul\Helpdesk\Helper\Data $dataHelper,
        \Webkul\Helpdesk\Model\TicketsFactory $ticketsFactory,
        \Webkul\Helpdesk\Model\ThreadFactory $threadFactory,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Filesystem\Driver\File $filesystemFile,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Escaper $escaper,
        \Magento\Eav\Model\Entity\Attribute\Source\Table $sorceTable,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;
        $this->_ticketsHelper = $ticketsHelper;
        $this->_ticketsFactory = $ticketsFactory;
        $this->_threadFactory = $threadFactory;
        $this->_eavAttribute = $eavAttribute;
        $this->assetRepo = $assetRepo;
        $this->jsonHelper = $jsonHelper;
        $this->filesystemFile = $filesystemFile;
        $this->escaper = $escaper;
        parent::__construct(
            $context,
            $supportcenterFactory,
            $cmspageFactory,
            $typeFactory,
            $ticketsPriorityFactory,
            $groupFactory,
            $ticketsStatusFactory,
            $ticketsCusAttrFactory,
            $eavAttribute,
            $ticketsHelper,
            $dataHelper,
            $jsonHelper,
            $resource,
            $sorceTable,
            $data
        );
    }

    /**
     * Get Current Ticket obejct
     *
     * @return Object
     */
    public function getCurrentTicket()
    {
        $ticketId = $this->getRequest()->getParam('id');
        return $this->_ticketsFactory->create()->load($ticketId);
    }

    /**
     * Return threads according to config limit setting
     *
     * @param int $maxThreadLimit
     * @param int $ticketId
     * @return Object $collection Threads Collection
     */
    public function getCollection($maxThreadLimit, $ticketId)
    {
        $collection = $this->_threadFactory->create()->getCollection()
            ->addFieldToFilter("ticket_id", $ticketId)
            ->addFieldToFilter("thread_type", ["eq"=>"reply"]);
        $collection->setOrder('entity_id', 'DESC');
        $collection->setPageSize($maxThreadLimit);
        $collection->setCurPage($this->getCurrentPage());
        return $collection;
    }

    /**
     * This function returns the current page count
     *
     * @return int
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
     * Get attribute data
     *
     * @param int $attributeId
     * @return object
     */
    public function getAttributeData($attributeId)
    {
        return $this->_eavAttribute->load($attributeId);
    }

    /**
     * Get pdf url
     *
     * @return string
     */
    public function pdfFileUrl()
    {
        return $this->_assetRepo->getUrl("Webkul_Helpdesk::images/pdf.png");
    }

    /**
     * Get doc file url
     *
     * @return string
     */
    public function docFileUrl()
    {
        return $this->_assetRepo->getUrl("Webkul_Helpdesk::images/doc.png");
    }

    /**
     * Get ticket helper
     *
     * @return object
     */
    public function getTicketHelper()
    {
        return $this->_ticketsHelper;
    }

    /**
     * Get data helper
     *
     * @return object
     */
    public function getDataHelper()
    {
        return $this->dataHelper;
    }

    /**
     * Get json helper
     *
     * @return object
     */
    public function getJsonHelper()
    {
        return $this->jsonHelper;
    }

    /**
     * Check if file/directory exists
     *
     * @param string $data
     * @return object
     */
    public function filexists($data)
    {
        return $this->filesystemFile->isExists($data);
    }

    /**
     * GetTicketUrl
     *
     * @param int $Id
     */
    public function getTicketUrl($Id)
    {
        $path = 'helpdesk/ticket/view';
        $url = $this->_urlBuilder->getUrl($path, ['id' => $Id]);
        return $this->escaper->escapeUrl((string)$url);
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
