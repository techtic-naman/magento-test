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
namespace Webkul\Helpdesk\Controller\Adminhtml\Ticketsmanagement\Tickets;

use Magento\Framework\Exception\AuthenticationException;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Webkul\Helpdesk\Model\TicketsRepository
     */
    protected $_ticketsRepo;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * @var \Magento\User\Model\UserFactory
     */
    protected $_userFactory;

    /**
     * @var \Webkul\Helpdesk\Model\ActivityRepository
     */
    protected $_activityRepo;

    /**
     * @var \Webkul\Helpdesk\Logger\HelpdeskLogger
     */
    protected $_helpdeskLogger;

    /**
     * @param Context                                     $context
     * @param PageFactory                                 $resultPageFactory
     * @param \Webkul\Helpdesk\Model\TicketsRepository    $ticketsRepo
     * @param \Magento\Backend\Model\Auth\Session         $authSession
     * @param \Magento\User\Model\UserFactory             $userFactory
     * @param \Webkul\Helpdesk\Model\ActivityRepository   $activityRepo
     * @param \Webkul\Helpdesk\Logger\HelpdeskLogger      $helpdeskLogger
     * @param \Webkul\Helpdesk\Model\ThreadRepository     $threadRepo
     * @param \Webkul\Helpdesk\Model\EventsRepository     $eventsRepo
     * @param \Webkul\Helpdesk\Model\AttachmentRepository $attachmentRepo
     * @param \Webkul\Helpdesk\Model\ThreadFactory        $threadFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Webkul\Helpdesk\Model\TicketsRepository $ticketsRepo,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\User\Model\UserFactory $userFactory,
        \Webkul\Helpdesk\Model\ActivityRepository $activityRepo,
        \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger,
        \Webkul\Helpdesk\Model\ThreadRepository $threadRepo,
        \Webkul\Helpdesk\Model\EventsRepository $eventsRepo,
        \Webkul\Helpdesk\Model\AttachmentRepository $attachmentRepo,
        \Webkul\Helpdesk\Model\ThreadFactory $threadFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_ticketsRepo = $ticketsRepo;
        $this->_authSession = $authSession;
        $this->_userFactory = $userFactory;
        $this->_activityRepo = $activityRepo;
        $this->_helpdeskLogger = $helpdeskLogger;
        $this->_threadRepo = $threadRepo;
        $this->_eventsRepo = $eventsRepo;
        $this->_attachmentRepo = $attachmentRepo;
        $this->_threadFactory = $threadFactory;
    }

    /**
     * Save ticket
     *
     * @return void
     */
    public function execute()
    {
        try {
            $data = $this->getRequest()->getPostValue();
            if (!$data) {
                $this->messageManager->addError(__('Unable to find ticket to save'));
                return $this->resultRedirectFactory->create()->setPath('helpdesk/*/');
            }
            $data['source'] = "website";
            $ticketId = $this->_ticketsRepo->createTicket($data);
            $agentId = $this->_authSession->getUser()->getId();
            $agent = $this->_userFactory->create()->load($agentId);
            $this->_activityRepo->saveActivity($ticketId, $agent->getName(), "add", "ticket");
            $data['thread_type'] = "create";
            $data['source'] = "website";
            $data['checkwhois'] = 1;
            $data['is_admin'] = 1;
            $data['customer_id'] = $agentId;
            $threadId = $this->_threadRepo->createThread($ticketId, $data);
            $files = $this->getRequest()->getFiles();
            if (isset($files["fupld"]["tmp_name"][0])) {
                $this->_attachmentRepo->saveAttachment($ticketId, $threadId);
            } else {
                $this->_threadFactory->create()->load($threadId)->setAttachment(0)
                    ->save();
            }
            $this->_eventsRepo->checkTicketEvent("ticket", $ticketId, "created");
            $this->_authSession->setFormData(false);
            $this->messageManager->addSuccess(__('Ticket has been successfully created'));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->_helpdeskLogger->info($e->getMessage());
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_helpdeskLogger->info($e->getMessage());
            $this->messageManager->addError("There are some error occurring during this action");
        }
        return $this->resultRedirectFactory->create()->setPath('helpdesk/*/');
    }

    /**
     * Check save Helpdesk Events Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Helpdesk::tickets');
    }
}
