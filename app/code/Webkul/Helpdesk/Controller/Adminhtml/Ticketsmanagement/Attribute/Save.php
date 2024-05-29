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

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Webkul\Helpdesk\Logger\HelpdeskLogger
     */
    protected $_helpdeskLogger;

    /**
     * @var \Webkul\Helpdesk\Model\Eav\CustomAttributeFactory
     */
    protected $_eavCustomAttrFactory;

    /**
     * @var \Webkul\Helpdesk\Model\ActivityRepository
     */
    protected $_activityRepository;

    /**
     * @var \Magento\Eav\Model\Entity
     */
    protected $_eavEntity;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute
     */
    protected $_eavEntityAttr;

    /**
     * @var \Webkul\Helpdesk\Model\TicketsCustomAttributesFactory
     */
    protected $_ticketCustomAttrFactory;

    /**
     * @param Context                                               $context
     * @param PageFactory                                           $resultPageFactory
     * @param \Webkul\Helpdesk\Logger\HelpdeskLogger                $helpdeskLogger,
     * @param \Webkul\Helpdesk\Model\Eav\CustomAttributeFactory     $eavCustomAttrFactory,
     * @param \Webkul\Helpdesk\Model\ActivityRepository             $activityRepository,
     * @param \Magento\Eav\Model\Entity                             $eavEntity,
     * @param \Magento\Eav\Model\Entity\Attribute                   $eavEntityAttr,
     * @param \Webkul\Helpdesk\Model\TicketsCustomAttributesFactory $ticketCustomAttrFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger,
        \Webkul\Helpdesk\Model\Eav\CustomAttributeFactory $eavCustomAttrFactory,
        \Webkul\Helpdesk\Model\ActivityRepository $activityRepository,
        \Magento\Eav\Model\Entity $eavEntity,
        \Magento\Eav\Model\Entity\Attribute $eavEntityAttr,
        \Webkul\Helpdesk\Model\TicketsCustomAttributesFactory $ticketCustomAttrFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_helpdeskLogger = $helpdeskLogger;
        $this->_eavCustomAttrFactory = $eavCustomAttrFactory;
        $this->_activityRepository = $activityRepository;
        $this->_eavEntity = $eavEntity;
        $this->_eavEntityAttr = $eavEntityAttr;
        $this->_ticketCustomAttrFactory = $ticketCustomAttrFactory;
    }

    /**
     * Save Attribute
     *
     * @return void
     */
    public function execute()
    {
        try {
            $data = $this->getRequest()->getPostValue();
            $attrId = isset($data['attribute_id'])?$data['attribute_id']:0;
            if (empty($data)) {
                $this->messageManager->addErrorMessage(__('Unable to find attribute to save'));
                return $this->resultRedirectFactory->create()->setPath('helpdesk/*/');
            }
            if ($attrId) {
                $model = $this->_eavCustomAttrFactory->create()->load($attrId);
                $model->setData($data);
                $model->save();
                $this->_activityRepository->saveActivity(
                    $attrId,
                    $model->getFrontendLabel(),
                    "edit",
                    "ticketcustomfield"
                );
            } else {
                $model = $this->_eavCustomAttrFactory->create();
                $model->setData($data);
                $model->save();
                $this->_activityRepository->saveActivity(
                    $model->getId(),
                    $model->getFrontendLabel(),
                    "add",
                    "ticketcustomfield"
                );
            }
            $id = $model->getId();
            $data['entity_type_id'] = $this->_eavEntity->setType('ticketsystem_ticket')->getTypeId();
            if (isset($data['entity_type_id']) && isset($data['attribute_code']) && $data['attribute_code']) {
                $attribute = $this->_eavEntityAttr->loadByCode('ticketsystem_ticket', $data['attribute_code']);
                if ($data['frontend_input'] == 'boolean') {
                    $attribute->setData('source_model', \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class);
                }
                $attribute->save();
                $ticketAttribute = $this->_ticketCustomAttrFactory->create()->getCollection()
                    ->addFieldToFilter('attribute_id', ['eq'=>$attribute->getAttributeId()])
                    ->getFirstItem();
                if (!$ticketAttribute->getIndexId()) {
                    $ticketAttributeTemp = $this->_ticketCustomAttrFactory->create();
                    $ticketAttributeTemp->setShowInFront(0);
                    $ticketAttributeTemp->setAttributeId($attribute->getId());
                    $ticketAttributeTemp->setFieldDependency($data['field_dependency']);
                    $ticketAttributeTemp->setStatus($data['is_visible']);
                    $ticketAttributeTemp->save();
                }
            } else {
                $ticketAttributeCollection = $this->_ticketCustomAttrFactory->create()->getCollection()
                    ->addFieldToFilter('attribute_id', ['eq'=>$attrId]);
                foreach ($ticketAttributeCollection as $ticketAttribute) {
                    $ticketAttribute->setFieldDependency($data['field_dependency']);
                    $ticketAttribute->setStatus($data['is_visible']);
                    $ticketAttribute->save();
                }
            }
            $this->messageManager->addSuccessMessage(__("Custom attribute successfully saved"));
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
        return $this->_authorization->isAllowed('Webkul_Helpdesk::customattribute');
    }
}
