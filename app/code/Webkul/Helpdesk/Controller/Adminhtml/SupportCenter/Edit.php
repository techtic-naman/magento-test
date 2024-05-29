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
namespace Webkul\Helpdesk\Controller\Adminhtml\SupportCenter;

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
     * @var \Webkul\Helpdesk\Model\SupportCenterFactory
     */
    protected $_supportCenterFactory;

    /**
     * @param Action\Context                              $context
     * @param \Magento\Framework\View\Result\PageFactory  $resultPageFactory
     * @param \Magento\Framework\Registry                 $registry
     * @param \Webkul\Helpdesk\Model\SupportCenterFactory $supportCenterFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Webkul\Helpdesk\Model\SupportCenterFactory $supportCenterFactory
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->_supportCenterFactory = $supportCenterFactory;
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
        $resultPage->setActiveMenu('Webkul_Helpdesk::supportcenter')
            ->addBreadcrumb(__('Add Item'), __('Add Item'))
            ->addBreadcrumb(__('Manage Item'), __('Manage Item'));
        return $resultPage;
    }

    /**
     * Edit Supportcenter information
     *
     * @return void
     */
    public function execute()
    {
        $scId = $this->getRequest()->getParam('id');
        $model = $this->_supportCenterFactory->create();

        if ($scId) {
            $model->load($scId);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Item no longer exists.'));
                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }
        }

        // Restore previously entered form data from session
        $data = $this->_session->getUserData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->_coreRegistry->register('support_center', $model);

        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Item'));
        $resultPage->getConfig()->getTitle()->prepend(
            $model->getId() ?
            $model->getName() : __('New Item')
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
        return $this->_authorization->isAllowed('Webkul_Helpdesk::supportcenter');
    }
}
