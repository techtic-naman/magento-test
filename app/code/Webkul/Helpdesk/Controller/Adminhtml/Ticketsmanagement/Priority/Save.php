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
namespace Webkul\Helpdesk\Controller\Adminhtml\Ticketsmanagement\Priority;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Webkul\Helpdesk\Model\TicketsPriorityFactory
     */
    protected $_ticketsPriorityFactory;

    /**
     * @var \Webkul\Helpdesk\Logger\HelpdeskLogger
     */
    protected $_helpdeskLogger;

    /**
     * @var \Webkul\Helpdesk\Model\ActivityRepository
     */
    protected $_activityRepo;

    /**
     * @param Context                                       $context
     * @param PageFactory                                   $resultPageFactory
     * @param \Webkul\Helpdesk\Logger\HelpdeskLogger        $helpdeskLogger
     * @param \Webkul\Helpdesk\Model\TicketsPriorityFactory $ticketsPriorityFactory
     * @param \Webkul\Helpdesk\Model\ActivityRepository     $activityRepo
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger,
        \Webkul\Helpdesk\Model\TicketsPriorityFactory $ticketsPriorityFactory,
        \Webkul\Helpdesk\Model\ActivityRepository $activityRepo
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_helpdeskLogger = $helpdeskLogger;
        $this->_ticketsPriorityFactory = $ticketsPriorityFactory;
        $this->_activityRepo = $activityRepo;
    }

    /**
     * Save Priority
     *
     * @return void
     */
    public function execute()
    {
        try {
            $data = $this->getRequest()->getPostValue();
            $priorityId = $this->getRequest()->getParam('entity_id');
            if ($this->getRequest()->getPost()) {
                $model = $this->_ticketsPriorityFactory->create();
                $model->setData($data)->setId($priorityId);
                $model->save();
                if ($priorityId) {
                    $this->_activityRepo->saveActivity($priorityId, $model->getName(), "edit", "priority");
                } else {
                    $this->_activityRepo->saveActivity($model->getId(), $model->getName(), "add", "priority");
                }
                $this->messageManager->addSuccessMessage(__("Priority successfully saved"));
            } else {
                $this->messageManager->addErrorMessage(__('Unable to find ticket Priority to save'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__("There are some error to save type"));
            $this->_helpdeskLogger->info($e->getMessage());
        }
        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }

    /**
     * Check save action Helpdesk Events Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Helpdesk::priority');
    }
}
