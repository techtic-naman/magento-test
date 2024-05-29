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
namespace Webkul\Helpdesk\Controller\Adminhtml\Ticketsmanagement\Attribute;

use Magento\Backend\App\Action;

class Edit extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $_resultPageFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    private $_coreRegistry;

    /**
     * @var \Webkul\Helpdesk\Model\TicketsCustomAttributesFactory
     */
    private $_ticketsCustomAttributesFactory;

    /**
     * @var \Magento\Eav\Model\Entity\AttributeFactory
     */
    private $_eavEntityAttrFactory;

    /**
     * @param Action\Context                                        $context
     * @param \Magento\Framework\View\Result\PageFactory            $resultPageFactory
     * @param \Magento\Framework\Registry                           $registry
     * @param \Webkul\Helpdesk\Model\TicketsCustomAttributesFactory $ticketsCustomAttributesFactory
     * @param \Magento\Eav\Model\Entity\AttributeFactory            $eavEntityAttrFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Webkul\Helpdesk\Model\TicketsCustomAttributesFactory $ticketsCustomAttributesFactory,
        \Magento\Eav\Model\Entity\AttributeFactory $eavEntityAttrFactory
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->_ticketsCustomAttributesFactory = $ticketsCustomAttributesFactory;
        $this->_eavEntityAttrFactory = $eavEntityAttrFactory;
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
        $resultPage->setActiveMenu('Webkul_Helpdesk::customattribute')
            ->addBreadcrumb(__('Add Attribute'), __('Add Attribute'))
            ->addBreadcrumb(__('Manage Attribute'), __('Manage Attribute'));
        return $resultPage;
    }

    /**
     * Edit attribute action
     *
     * @return void
     */
    public function execute()
    {
        $attributeId = (int)$this->getRequest()->getParam('id');
        $attributemodel = $this->_eavEntityAttrFactory->create();
        if ($attributeId) {
            $customattrmodel = $this->_ticketsCustomAttributesFactory->create();
            $customattrmodel->load($attributeId);
            if (!$customattrmodel->getId()) {
                $this->messageManager->addErrorMessage(__('This custom attribute no longer exists.'));
                $this->resultRedirectFactory->create()->setPath('helpdesk/*/');
                return;
            }
            $attributemodel->load($customattrmodel->getAttributeId());
            $attributemodel->setFieldDependency($customattrmodel->getFieldDependency());
            $attributemodel->setIsVisible($customattrmodel->getStatus());
            $attributemodel->setAttributeLabel($attributemodel->getFrontendLabel());
        }

        $this->_coreRegistry->register('helpdesk_ticket_attribute', $attributemodel);

        if (isset($attributeId)) {
            $breadcrumb = __('Edit Custom Attribute');
        } else {
            $breadcrumb = __('New Custom Attribute');
        }

        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Attribute'));
        $resultPage->getConfig()->getTitle()->prepend(
            $attributemodel->getId() ?
            $attributemodel->getName() : __('New Custom Attribute')
        );
        return $resultPage;
    }

    /**
     * Check Edit attribute Helpdesk Events Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Helpdesk::customattribute');
    }
}
