<?php
/**
 * Webkul Software.
 *
 * @category Webkul
 * @package  Webkul_Helpdesk
 * @author   Webkul
 * @license  https://store.webkul.com/license.html
 */
namespace Webkul\Helpdesk\Controller\Adminhtml\Customer\Organization;

use Magento\Framework\Exception\AuthenticationException;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Webkul\Helpdesk\Model\CustomerOrganizationFactory
     */
    protected $_customerOrgFactory;

    /**
     * @var \Webkul\Helpdesk\Logger\HelpdeskLogger
     */
    protected $_helpdeskLogger;

    /**
     * @param Context                                            $context
     * @param PageFactory                                        $resultPageFactory
     * @param \Webkul\Helpdesk\Logger\HelpdeskLogger             $helpdeskLogger
     * @param \Webkul\Helpdesk\Model\CustomerOrganizationFactory $customerOrgFactory
     * @param \Webkul\Helpdesk\Model\ActivityRepository          $activityRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger,
        \Webkul\Helpdesk\Model\CustomerOrganizationFactory $customerOrgFactory,
        \Webkul\Helpdesk\Model\ActivityRepository $activityRepository
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_helpdeskLogger = $helpdeskLogger;
        $this->_customerOrgFactory = $customerOrgFactory;
        $this->_activityRepository = $activityRepository;
    }

    /**
     * Execute method
     *
     * @return void
     */
    public function execute()
    {
        try {
            $data = $this->getRequest()->getPostValue();
            $orgId = isset($data['entity_id'])?$data['entity_id']:0;
            if (empty($data)) {
                $this->messageManager->addError(__('Unable to find organization to save'));
                return $this->resultRedirectFactory->create()->setPath('helpdesk/*/');
            }
            if (array_key_exists("customers", $data)) {
                $data["customers"] = implode(",", $data["customers"]);
            }
            if (array_key_exists("groups", $data)) {
                 $data["groups"] = implode(",", $data["groups"]);
            }
            if ($orgId) {
                $model = $this->_customerOrgFactory->create()->load($orgId);
                $model->setData($data);
                $model->save();
                $this->_activityRepository->saveActivity($orgId, $model->getName(), "edit", "organization");
            } else {
                $model = $this->_customerOrgFactory->create();
                $model->setName($data["name"]);
                $model->setDescription($data["description"]);
                $model->setDomain($data["domain"]);
                $model->setNotes($data["notes"]);
                $model->setCustomers(isset($data["customers"])?$data["customers"]:'');
                $model->setCustomerRole($data["customer_role"]);
                $model->setGroups(isset($data["groups"])?$data["groups"]:'');
            
                $model->save();
                $this->_activityRepository->saveActivity($model->getId(), $model->getName(), "add", "customer");
            }
            $this->messageManager->addSuccess(__("Organization successfully saved"));
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
            $this->_helpdeskLogger->info($e->getMessage());
            return $this->resultRedirectFactory->create()->setPath("*/*/edit", ["id" => $orgId]);
        }
    }

    /**
     * Check Save Helpdesk Events Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Helpdesk::customerorganization');
    }
}
