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

use Magento\Eav\Model\Entity\Attribute\Source\Table;

class Navigation extends \Magento\Framework\View\Element\Template
{
    public const TABLE_NAME = "cms_page";

    /**
     * @param \Magento\Framework\View\Element\Template\Context      $context
     * @param \Webkul\Helpdesk\Model\SupportCenterFactory           $supportcenterFactory
     * @param \Magento\Cms\Model\PageFactory                        $cmspageFactory
     * @param \Webkul\Helpdesk\Model\TypeFactory                    $typeFactory
     * @param \Webkul\Helpdesk\Model\TicketsPriorityFactory         $ticketsPriorityFactory
     * @param \Webkul\Helpdesk\Model\GroupFactory                   $groupFactory
     * @param \Webkul\Helpdesk\Model\TicketsStatusFactory           $ticketsStatusFactory
     * @param \Webkul\Helpdesk\Model\TicketsCustomAttributesFactory $ticketsCusAttrFactory
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute    $eavAttribute
     * @param \Webkul\Helpdesk\Helper\Tickets                       $tickets
     * @param \Webkul\Helpdesk\Helper\Data                          $helpdeskTickets
     * @param \Magento\Framework\Json\Helper\Data                   $jsonData
     * @param \Magento\Framework\App\ResourceConnection             $resource
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
        \Webkul\Helpdesk\Helper\Tickets $tickets,
        \Webkul\Helpdesk\Helper\Data $helpdeskTickets,
        \Magento\Framework\Json\Helper\Data $jsonData,
        \Magento\Framework\App\ResourceConnection $resource,
        Table $sourceTable,
        array $data = []
    ) {
        $this->_supportcenterFactory = $supportcenterFactory;
        $this->_cmspageFactory = $cmspageFactory;
        $this->_typeFactory = $typeFactory;
        $this->_ticketsPriorityFactory = $ticketsPriorityFactory;
        $this->_groupFactory = $groupFactory;
        $this->_ticketsStatusFactory = $ticketsStatusFactory;
        $this->_ticketsCusAttrFactory = $ticketsCusAttrFactory;
        $this->_eavAttribute = $eavAttribute;
        $this->tickets = $tickets;
        $this->helpdeskTickets = $helpdeskTickets;
        $this->jsonData = $jsonData;
        $this->sourceTable = $sourceTable;
        $this->resource = $resource->getConnection();
        parent::__construct($context, $data);
    }

    /**
     * Return support center collection
     *
     * @return object
     */
    public function getSupportCenterData()
    {
        $params = $this->getRequest()->getParam('serach');
        $cmsTable = $this->resource->getTableName(self::TABLE_NAME);
        $supportCenter = $this->_supportcenterFactory->create()->getCollection()
            ->addFieldToFilter("status", ["eq"=>1]);
        if ($params) {
            $supportCenter->getSelect()
                ->join(
                    ['cms'=>$cmsTable],
                    'main_table.cms_id = cms.identifier',
                    ['cms.*']
                )
                ->where('title = "'.$params.'"');
        }
        return $supportCenter;
    }

    /**
     * Get cms page
     *
     * @return object
     */
    public function getCmsPages()
    {
        $cmsTable = $this->resource->getTableName(self::TABLE_NAME);
        $supportCenter = $this->_supportcenterFactory->create()->getCollection()
            ->addFieldToFilter("status", ["eq"=>1]);
        return $supportCenter;
    }

    /**
     * Get cms page
     *
     * @param int $cmsId
     * @param string $column
     * @return object
     */
    public function getCmsPage($cmsId, $column = 'identifier')
    {
        return $this->_cmspageFactory->create()->load($cmsId, $column);
    }

    /**
     * Get ticket type
     *
     * @return array
     */
    public function getTicketTypeArray()
    {
        return $this->_typeFactory->create()->toOptionArray();
    }

    /**
     * Get ticket priority
     *
     * @return array
     */
    public function getTicketPriorityArray()
    {
        return $this->_ticketsPriorityFactory->create()->toOptionArray();
    }

    /**
     * Get ticket group
     *
     * @return array
     */
    public function getTicketGroupArray()
    {
        return $this->_groupFactory->create()->toOptionArray();
    }

    /**
     * Get ticket status
     *
     * @return array
     */
    public function getTicketStatusArray()
    {
        return $this->_ticketsStatusFactory->create()->toOptionArray();
    }

    /**
     * Get Custom Attribute Collection
     *
     * @return object
     */
    public function getTicketcustomAttributeCollection()
    {
        return $this->_ticketsCusAttrFactory->create()->getCollection()
            ->addFieldToFilter("status", ["eq"=>1]);
    }

    /**
     * Get attribute by id
     *
     * @param int $attributeId
     * @return object
     */
    public function getAttributeById($attributeId)
    {
        return $this->_eavAttribute->load($attributeId);
    }

    /**
     * Get ticket helper
     *
     * @return object
     */
    public function ticketHelper()
    {
        return $this->tickets;
    }

    /**
     * Get data helper
     *
     * @return object
     */
    public function dataHelper()
    {
        return $this->helpdeskTickets;
    }

    /**
     * Get json data
     *
     * @return object
     */
    public function jsonData()
    {
        return $this->jsonData;
    }

    /**
     * GetSelectSourceModel
     */
    public function getSelectSourceModel($attribute)
    {
        return $this->sourceTable->setAttribute($attribute);
    }
}
