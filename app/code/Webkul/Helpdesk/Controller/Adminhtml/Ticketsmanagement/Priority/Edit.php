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

use Magento\Backend\App\Action;

class Edit extends Action
{
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Webkul\Helpdesk\Model\TicketsPriorityFactory
     */
    protected $_ticketPriorityFactory;

    /**
     * @param Action\Context                                $context
     * @param \Magento\Framework\View\Result\PageFactory    $resultPageFactory
     * @param \Magento\Framework\Registry                   $registry
     * @param \Webkul\Helpdesk\Model\TicketsPriorityFactory $ticketPriorityFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Webkul\Helpdesk\Model\TicketsPriorityFactory $ticketPriorityFactory
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->_ticketPriorityFactory = $ticketPriorityFactory;
        parent::__construct($context);
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /**
        * @var \Magento\Backend\Model\View\Result\Page $resultPage
        */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Webkul_Helpdesk::priority')
            ->addBreadcrumb(__('Add Priority'), __('Add Priority'))
            ->addBreadcrumb(__('Manage Priority'), __('Manage Priority'));
        return $resultPage;
    }

    /**
     * Edit priority action
     *
     * @return void
     */
    public function execute()
    {
        $priorityId = (int)$this->getRequest()->getParam('id');
        $prioritymodel = $this->_ticketPriorityFactory->create();
        if ($priorityId) {
            $prioritymodel->load($priorityId);
            if (!$prioritymodel->getId()) {
                $this->messageManager->addErrorMessage(__('This ticket priority no longer exists.'));
                $this->resultRedirectFactory->create()->setPath('helpdesk/*/');
                return;
            }
        }

        $this->_coreRegistry->register('helpdesk_ticket_priority', $prioritymodel);

        if (isset($priorityId)) {
            $breadcrumb = __('Edit Priority');
        } else {
            $breadcrumb = __('New Priority');
        }
        
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Priority'));
        $resultPage->getConfig()->getTitle()->prepend(
            $prioritymodel->getId() ?
            $prioritymodel->getName() : __('New Priority')
        );
        return $resultPage;
    }

    /**
     * Check edit action Helpdesk Events Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Helpdesk::priority');
    }
}
