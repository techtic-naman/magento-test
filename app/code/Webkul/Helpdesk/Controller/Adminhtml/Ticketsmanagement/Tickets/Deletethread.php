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

class Deletethread extends \Magento\Backend\App\Action
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
     * @param \Webkul\Helpdesk\Model\ThreadFactory      $threadFactory
     * @param \Magento\Backend\Model\Auth\Session       $authSession
     * @param \Webkul\Helpdesk\Model\ActivityRepository $activityRepo
     * @param \Webkul\Helpdesk\Logger\HelpdeskLogger    $helpdeskLogger
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Webkul\Helpdesk\Model\ThreadFactory $threadFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Webkul\Helpdesk\Model\ActivityRepository $activityRepo,
        \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_threadFactory = $threadFactory;
        $this->_authSession = $authSession;
        $this->_activityRepo = $activityRepo;
        $this->_helpdeskLogger = $helpdeskLogger;
    }

    /**
     * Delete thread
     *
     * @return void
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam("id");
        $this->_activityRepo->saveActivity($id, "Query", "delete", "ticket");
        $this->_threadFactory->create()->load($id)->delete();
        $this->getResponse()->setHeader('Content-type', 'text/html');
        $this->getResponse()->setBody(1);
    }

    /**
     * Check delete thread Helpdesk Events Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Helpdesk::tickets');
    }
}
