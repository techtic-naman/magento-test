<?php
/**
 * Webkul Software.
 *
 * @category Webkul
 * @package  Webkul_Helpdesk
 * @author   Webkul
 * @license  https://store.webkul.com/license.html
 */
namespace Webkul\Helpdesk\Controller\Adminhtml\EmailTemplate;

use Magento\Backend\App\Action;

class Edit extends Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @param Action\Context                              $context
     * @param \Magento\Framework\View\Result\PageFactory  $resultPageFactory
     * @param \Magento\Framework\Registry                 $registry
     * @param \Webkul\Helpdesk\Model\EmailTemplateFactory $emailtemplateFactory
     * @param \Magento\Email\Model\BackendTemplate        $emailbackendTemp
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Webkul\Helpdesk\Model\EmailTemplateFactory $emailtemplateFactory,
        \Magento\Email\Model\BackendTemplate $emailbackendTemp
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->_emailtemplateFactory = $emailtemplateFactory;
        $this->_emailbackendTemp = $emailbackendTemp;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Helpdesk::emailtemplate');
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Webkul_Helpdesk::emailtemplate')
            ->addBreadcrumb(__('Email Template'), __('Events'))
            ->addBreadcrumb(__('Manage Email Template'), __('Manage Email Template'));
        return $resultPage;
    }

    /**
     * Edit Tickets
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $emailTempId = (int)$this->getRequest()->getParam('id');
        $emailtemplatemodel = $this->_emailbackendTemp;
        if ($emailTempId) {
            $emailtemplatemodel = $this->_emailbackendTemp->load($emailTempId);
            if (!$emailtemplatemodel->getId()) {
                $this->messageManager->addError(__('This Email Template no longer exists.'));
                return $this->resultRedirectFactory->create()->setPath('helpdesk/*/');
            }
        }

        if (!$this->_coreRegistry->registry('email_template')) {
            $this->_coreRegistry->register('email_template', $emailtemplatemodel);
        }
        if (!$this->_coreRegistry->registry('current_email_template')) {
            $this->_coreRegistry->register('current_email_template', $emailtemplatemodel);
        }
        $resultPage = $this->_resultPageFactory->create();
        if ($emailTempId) {
            $breadcrumb = __('Edit Email Template');
            $resultPage->getConfig()->getTitle()->prepend(__('Edit Email Template'));

        } else {
            $breadcrumb = __('New Email Template');
            $resultPage->getConfig()->getTitle()->prepend(__('New Email Template'));
        }
        return $resultPage;
    }
}
