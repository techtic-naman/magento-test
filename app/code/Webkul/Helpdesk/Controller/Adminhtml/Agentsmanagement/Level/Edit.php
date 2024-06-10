<?php
/**
 * Webkul Software.
 *
 * @category Webkul
 * @package  Webkul_Helpdesk
 * @author   Webkul
 * @license  https://store.webkul.com/license.html
 */
namespace Webkul\Helpdesk\Controller\Adminhtml\Agentsmanagement\Level;

use Magento\Framework\Locale\Resolver;
use Magento\Backend\App\Action;

class Edit extends Action
{
    /**
     * @param Action\Context                             $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry                $registry
     * @param \Webkul\Helpdesk\Model\AgentLevelFactory   $agentLevelFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Webkul\Helpdesk\Model\AgentLevelFactory $agentLevelFactory
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->_agentLevelFactory = $agentLevelFactory;
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
        $resultPage->setActiveMenu('Webkul_Helpdesk::level')
            ->addBreadcrumb(__('Add Level'), __('Add Level'))
            ->addBreadcrumb(__('Manage Level'), __('Manage Level'));
        return $resultPage;
    }

    /**
     * Edit level information
     *
     * @return void
     */
    public function execute()
    {
        $levelId = $this->getRequest()->getParam('entity_id');
        $model = $this->_agentLevelFactory->create();
        if ($levelId) {
            $model->load($levelId);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This level no longer exists.'));
                $this->resultRedirectFactory->create()->setPath('adminhtml/*/');
                return;
            }
        }
        // Restore previously entered form data from session
        $data = $this->_session->getUserData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->_coreRegistry->register('agent_level', $model);

        if (isset($agentId)) {
            $breadcrumb = __('Edit Level');
        } else {
            $breadcrumb = __('New Level');
        }

        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Level'));
        $resultPage->getConfig()->getTitle()->prepend(
            $model->getId() ?
            $model->getName() : __('New Level')
        );
        return $resultPage;
    }

    /**
     * Check Edit action Helpdesk Events Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Helpdesk::level');
    }
}
