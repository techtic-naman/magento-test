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
namespace Webkul\Helpdesk\Controller\Ticket;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Webkul\Helpdesk\Model\ResourceModel\Ticketdraft\CollectionFactory;

/**
 * Reset ticket form
 */
class ResetForm extends Action
{
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var CollectionFactory
     */
    protected $_draftCollFactory;

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var \Webkul\Helpdesk\Helper\Tickets
     */
    protected $_ticketsHelper;

    /**
     * @param Context                         $context
     * @param PageFactory                     $resultPageFactory
     * @param DataPersistorInterface          $dataPersistor
     * @param CollectionFactory               $draftCollFactory
     * @param Session                         $customerSession
     * @param \Webkul\Helpdesk\Helper\Tickets $ticketsHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        DataPersistorInterface $dataPersistor,
        CollectionFactory $draftCollFactory,
        Session $customerSession,
        \Webkul\Helpdesk\Helper\Tickets $ticketsHelper
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->dataPersistor = $dataPersistor;
        $this->_draftCollFactory = $draftCollFactory;
        $this->_customerSession = $customerSession;
        $this->_ticketsHelper = $ticketsHelper;
        parent::__construct($context);
    }

    /**
     * Helpdesk Support page.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $this->dataPersistor->clear('new_ticket_form');
        $customer_id = $this->_customerSession->getCustomerId();
        if ($customer_id) {
            $userId = $this->_ticketsHelper->getTsCustomerId();
            if ($userId) {
                $draftColl = $this->_draftCollFactory->create()
                        ->addFieldToFilter("ticket_id", 0)
                        ->addFieldToFilter("user_id", $userId)
                        ->addFieldToFilter("user_type", ["eq"=>"customer"])
                        ->addFieldToFilter("field", "new");
                if ($draftColl->getSize()) {
                    $draft = $draftColl->getFirstItem();
                    $draft->delete();
                }
            }
        }
    }
}
