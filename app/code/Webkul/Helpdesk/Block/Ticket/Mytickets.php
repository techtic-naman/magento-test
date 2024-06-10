<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Helpdesk
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Helpdesk\Block\Ticket;

class Mytickets extends Navigation
{

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
     * @param \Webkul\Helpdesk\Helper\Tickets                       $ticketsHelper
     * @param \Webkul\Helpdesk\Model\TicketsFactory                 $ticketsFactory
     * @param \Webkul\Helpdesk\Helper\Data                          $helperData
     * @param \Magento\Framework\Json\Helper\Data                   $jsonData
     * @param \Magento\Framework\App\ResourceConnection             $resource
     * @param \Magento\Customer\Model\Session                       $session
     * @param array                                                 $data                                         $data
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
        \Webkul\Helpdesk\Model\TicketsFactory $ticketsFactory,
        \Webkul\Helpdesk\Helper\Data $helperData,
        \Magento\Framework\Json\Helper\Data $jsonData,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Customer\Model\Session $session,
        array $data = []
    ) {
        $this->_ticketsHelper = $ticketsHelper;
        $this->dataHelper = $helperData;
        $this->_ticketsFactory = $ticketsFactory;
        $this->session = $session;
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
            $helperData,
            $jsonData,
            $resource,
            $data
        );
    }

    /**
     * Get all tickets
     *
     * @return object
     */
    public function getAllTickets()
    {
        $customerId = $this->_ticketsHelper->getTsCustomerId();
        $ticketCollection = $this->_ticketsFactory->create()->getCollection()
            ->addFieldToFilter("customer_id", $customerId);
        $helpdesk_tickets_status = $ticketCollection->getTable("helpdesk_tickets_status");
        $helpdesk_tickets_type = $ticketCollection->getTable("helpdesk_tickets_type");
        $helpdesk_tickets_priority = $ticketCollection->getTable("helpdesk_tickets_priority");

        $ticketCollection->getSelect()
            ->joinRight(
                ["ce1" => $helpdesk_tickets_status],
                "ce1.entity_id = 
                main_table.status",
                ["status_name"=>"ce1.name"]
            );
        $ticketCollection->getSelect()
            ->joinRight(
                ["ce2" => $helpdesk_tickets_type],
                "ce2.entity_id = 
                main_table.type",
                ["type_name"=>"ce2.type_name"]
            );
        $ticketCollection->getSelect()
            ->joinRight(
                ["ce3" => $helpdesk_tickets_priority],
                "ce3.entity_id = 
                main_table.priority",
                ["priority_name"=>"ce3.name"]
            );

        $status = $this->getRequest()->getParam('status')!=""?$this->getRequest()->getParam('status'):"";
        $priority = $this->getRequest()->getParam('priority')!=""?$this->getRequest()->getParam('priority'):"";
        $sort = $this->getRequest()->getParam('sort')!=""?$this->getRequest()->getParam('sort'):"";
        if ($status != "") {
            $ticketCollection->addFieldToFilter("main_table.status", $status);
        } elseif ($priority != "") {
            $ticketCollection->addFieldToFilter("main_table.priority", $priority);
        } elseif ($sort != "") {
            $order = $this->getRequest()->getParam('order');
            if ($sort == "ticket") {
                $ticketCollection->setOrder("entity_id", $order);
            } elseif ($sort == "subject") {
                $ticketCollection->setOrder("subject", $order);
            } elseif ($sort == "type") {
                $ticketCollection->setOrder("type_name", $order);
            } elseif ($sort == "priority") {
                $ticketCollection->setOrder("priority_name", $order);
            } elseif ($sort == "date") {
                $ticketCollection->setOrder("created_at", $order);
            } elseif ($sort == "status") {
                $ticketCollection->setOrder("status_name", $order);
            } else {
                $ticketCollection->setOrder("entity_id", "desc");
            }
        } else {
            $ticketCollection->setOrder("entity_id", "desc");
        }
        return $this->setCollection($ticketCollection);
    }

    /**
     * Prepare layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getAllTickets()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'helpdesk.tickets.mytickets.pager'
            )->setCollection(
                $this->getAllTickets()
            );
            $this->setChild('pager', $pager);
        }
        return $this;
    }

    /**
     * Get page html
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
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
}
