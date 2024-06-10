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
namespace Webkul\Helpdesk\Controller\Adminhtml\Responses;

use Magento\Framework\Locale\Resolver;
use Magento\Backend\App\Action;

class Edit extends Action
{
    /**
     * @param Action\Context                             $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry                $registry
     * @param \Webkul\Helpdesk\Model\ResponsesFactory    $responsesFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Webkul\Helpdesk\Model\ResponsesFactory $responsesFactory
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->_responsesFactory = $responsesFactory;
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
        $resultPage->setActiveMenu('Webkul_Helpdesk::responses')
            ->addBreadcrumb(__('Add Responses'), __('Add Responses'))
            ->addBreadcrumb(__('Manage Responses'), __('Manage Responses'));
        return $resultPage;
    }

    /**
     * Edit response action
     *
     * @return void
     */
    public function execute()
    {
        $responseId = $this->getRequest()->getParam('id');
        $model = $this->_responsesFactory->create();

        if ($responseId) {
            $model->load($responseId);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This response no longer exists.'));
                return $this->resultRedirectFactory->create()->setPath('helpdesk/*/');
            }
        }

        // Restore previously entered form data from session
        $data = $this->_session->getUserData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->_coreRegistry->register('helpdesk_responses', $model);

        if (isset($groupId)) {
            $breadcrumb = __('Edit Responses');
        } else {
            $breadcrumb = __('New Responses');
        }

        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Responses'));
        $resultPage->getConfig()->getTitle()->prepend(
            $model->getId() ?
            $model->getName() : __('New Responses')
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
        return $this->_authorization->isAllowed('Webkul_Helpdesk::responses');
    }
}
