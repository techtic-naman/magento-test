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
namespace Webkul\Helpdesk\Controller\Adminhtml\Customer\Customer;

use Magento\Backend\App\Action;

class Edit extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Webkul\Helpdesk\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @param Action\Context                             $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry                $registry
     * @param \Webkul\Helpdesk\Model\CustomerFactory     $customerFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Webkul\Helpdesk\Model\CustomerFactory $customerFactory
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->_customerFactory = $customerFactory;
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
        $resultPage->setActiveMenu('Webkul_Helpdesk::customers')
            ->addBreadcrumb(__('Add Customer'), __('Add Customer'))
            ->addBreadcrumb(__('Manage Customer'), __('Manage Customer'));
        return $resultPage;
    }

    /**
     * Edit customers actions
     *
     * @return void
     */
    public function execute()
    {
        $helpdeskCustomerId = $this->getRequest()->getParam('id');
        $model = $this->_customerFactory->create();

        if ($helpdeskCustomerId) {
            $model->load($helpdeskCustomerId);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Customer no longer exists.'));
                $this->resultRedirectFactory->create()->setPath('adminhtml/*/');
                return;
            }
        }

        // Restore previously entered form data from session
        $data = $this->_session->getUserData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->_coreRegistry->register('helpdesk_customer', $model);

        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Customer'));
        $resultPage->getConfig()->getTitle()->prepend(
            $model->getId() ?
            $model->getName() : __('New Customer')
        );
        return $resultPage;
    }

    /**
     * Check Editcustomer Helpdesk Connect Email Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Helpdesk::customers');
    }
}
