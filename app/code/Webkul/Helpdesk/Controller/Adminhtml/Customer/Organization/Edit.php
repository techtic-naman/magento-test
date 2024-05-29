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
namespace Webkul\Helpdesk\Controller\Adminhtml\Customer\Organization;

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
     * @var \Webkul\Helpdesk\Model\CustomerOrganizationFactory
     */
    protected $_customerOrganizationFactory;

    /**
     * @param Action\Context                                     $context
     * @param \Magento\Framework\View\Result\PageFactory         $resultPageFactory
     * @param \Magento\Framework\Registry                        $registry
     * @param \Webkul\Helpdesk\Model\CustomerOrganizationFactory $customerOrganizationFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Webkul\Helpdesk\Model\CustomerOrganizationFactory $customerOrganizationFactory
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->_customerOrganizationFactory = $customerOrganizationFactory;
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
        $resultPage->setActiveMenu('Webkul_Helpdesk::customerorganization')
            ->addBreadcrumb(__('Add Organization'), __('Add Organization'))
            ->addBreadcrumb(__('Manage Organization'), __('Manage Organization'));
        return $resultPage;
    }

    /**
     * Edit organization action
     *
     * @return void
     */
    public function execute()
    {
        $orgId = $this->getRequest()->getParam('id');
        $model = $this->_customerOrganizationFactory->create();

        if ($orgId) {
            $model->load($orgId);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Organization no longer exists.'));
                $this->resultRedirectFactory->create()->setPath('adminhtml/*/');
                return;
            }
        }

        // Restore previously entered form data from session
        $data = $this->_session->getUserData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->_coreRegistry->register('customer_organization', $model);

        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Organization'));
        $resultPage->getConfig()->getTitle()->prepend(
            $model->getId() ?
            $model->getName() : __('New Organization')
        );
        return $resultPage;
    }

    /**
     * Check edit organisation Helpdesk Events Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Helpdesk::customerorganization');
    }
}
