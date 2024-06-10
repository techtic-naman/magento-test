<?php
/**
 * Webkul Software.
 *
 * @category Webkul
 * @package  Webkul_Helpdesk
 * @author   Webkul
 * @license  https://store.webkul.com/license.html
 */
namespace Webkul\Helpdesk\Controller\Ticket;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Saveindraft extends Action
{
   
    /**
     * @var \Webkul\Helpdesk\Logger\HelpdeskLogger
     */
    protected $_helpdeskLogger;

    /**
     * @param Context                                   $context
     * @param \Webkul\Helpdesk\Model\TicketdraftFactory $ticketdraftFactory
     * @param \Webkul\Helpdesk\Logger\HelpdeskLogger    $helpdeskLogger
     * @param \Webkul\Helpdesk\Helper\Tickets           $ticketsHelper
     */
    public function __construct(
        Context $context,
        \Webkul\Helpdesk\Model\TicketdraftFactory $ticketdraftFactory,
        \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger,
        \Webkul\Helpdesk\Helper\Tickets $ticketsHelper
    ) {
        parent::__construct($context);
        $this->_ticketdraftFactory = $ticketdraftFactory;
        $this->_helpdeskLogger = $helpdeskLogger;
        $this->_ticketsHelper = $ticketsHelper;
    }

    /**
     * Save draft
     *
     * @return void
     */
    public function execute()
    {
        $flag = 0;
        $wholedata = $this->getRequest()->getParams();
        if (isset($wholedata['create_form']) && $wholedata['create_form']) {
            $wholedata["id"] = 0;
            $wholedata["field"] = 'new';
        }
        $userId = $this->_ticketsHelper->getTsCustomerId();
        $ticketCollection = $this->_ticketdraftFactory->create()->getCollection()
            ->addFieldToFilter("ticket_id", ["eq"=>$wholedata["id"]])
            ->addFieldToFilter("user_id", $userId)
            ->addFieldToFilter("user_type", ["eq"=>"customer"])
            ->addFieldToFilter("field", ["eq"=>$wholedata["field"]]);
        if (count($ticketCollection)) {
            foreach ($ticketCollection as $draft) {
                $draft->setContent($wholedata["content"]);
                $draft->save();
                $flag = 1;
            }
        } else {
            $draft = $this->_ticketdraftFactory->create();
            $draft->setTicketId($wholedata["id"]);
            $draft->setUserId($userId);
            $draft->setUserType("customer");
            $draft->setContent($wholedata["content"]);
            $draft->setField($wholedata["field"]);
            $draft->save();
            $flag = 1;
        }
        $this->getResponse()->setHeader('Content-type', 'text/html');
        $this->getResponse()->setBody($flag);
    }
}
