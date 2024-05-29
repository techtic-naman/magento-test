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
namespace Webkul\Helpdesk\Controller\Adminhtml\SlaPolicy;

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
     * @var \Webkul\Helpdesk\Model\SlapolicyFactory
     */
    protected $_slapolicyFactory;

    /**
     * @param Action\Context                             $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry                $registry
     * @param \Webkul\Helpdesk\Model\SlapolicyFactory    $slapolicyFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Webkul\Helpdesk\Model\SlapolicyFactory $slapolicyFactory
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->_slapolicyFactory = $slapolicyFactory;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Helpdesk::sla');
    }

    /**
     * Edit Slapolicy
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $slaId = $this->getRequest()->getParam('id');
        $slamodel = $this->_slapolicyFactory->create();
        if ($slaId) {
            $slamodel->load($slaId);
            if (!$slamodel->getId()) {
                $this->messageManager->addErrorMessage(__('This SLA Policy no longer exists.'));
                return $this->resultRedirectFactory->create()->setPath('helpdesk/*/');
            }
        }

        $this->_coreRegistry->register('helpdesk_slapolicy', $slamodel);
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Webkul_Helpdesk::sla');
        if ($slaId) {
            $resultPage->getConfig()->getTitle()->prepend(__('Edit SLA Policy'));

        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New SLA Policy'));
        }
        return $resultPage;
    }
}
