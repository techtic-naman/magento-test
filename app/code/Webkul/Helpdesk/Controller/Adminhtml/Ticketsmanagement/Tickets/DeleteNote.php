<?php
/**
 * Webkul Software.
 *
 * @category Webkul
 * @package  Webkul_Helpdesk
 * @author   Webkul
 * @license  https://store.webkul.com/license.html
 */
namespace Webkul\Helpdesk\Controller\Adminhtml\Ticketsmanagement\Tickets;

use Magento\Framework\Exception\AuthenticationException;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class DeleteNote extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Webkul\Helpdesk\Logger\HelpdeskLogger
     */
    protected $_helpdeskLogger;

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
     * Delete note
     *
     * @return void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam("id");
        $noteModel = $this->_ticketnotesFactory->create()->load($id)->delete();
        $this->getResponse()->setHeader('Content-type', 'text/html');
        $this->getResponse()->setBody(1);
    }

    /**
     * Check delete note Helpdesk Events Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Helpdesk::tickets');
    }
}
