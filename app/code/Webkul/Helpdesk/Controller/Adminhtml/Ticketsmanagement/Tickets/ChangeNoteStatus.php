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

class ChangeNoteStatus extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Webkul\Helpdesk\Model\TicketnotesFactory
     */
    protected $_ticketnotesFactory;

    /**
     * @var \Webkul\Helpdesk\Logger\HelpdeskLogger
     */
    protected $_helpdeskLogger;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * @param Context                                   $context
     * @param PageFactory                               $resultPageFactory
     * @param \Webkul\Helpdesk\Model\TicketnotesFactory $ticketnotesFactory
     * @param \Magento\Backend\Model\Auth\Session       $authSession
     * @param \Webkul\Helpdesk\Logger\HelpdeskLogger    $helpdeskLogger
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Webkul\Helpdesk\Model\TicketnotesFactory $ticketnotesFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_ticketnotesFactory = $ticketnotesFactory;
        $this->_authSession = $authSession;
        $this->_helpdeskLogger = $helpdeskLogger;
    }

    /**
     * Change note status
     *
     * @return void
     */
    public function execute()
    {
        $wholedata = $this->getRequest()->getParams();
        $noteModel = $this->_ticketnotesFactory->create()->load($wholedata['id']);
        $noteModel->setStatus($wholedata['status']);
        $noteModel->save();
        $this->getResponse()->setHeader('Content-type', 'text/html');
        $this->getResponse()->setBody(1);
    }

    /**
     * Check ChangeNoteStatus Helpdesk Events Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Helpdesk::tickets');
    }
}