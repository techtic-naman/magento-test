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

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Webkul\Helpdesk\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Webkul\Helpdesk\Logger\HelpdeskLogger
     */
    protected $_helpdeskLogger;

    /**
     * @var \Webkul\Helpdesk\Model\ActivityRepository
     */
    protected $_activityRepository;

    /**
     * @param Context                                   $context
     * @param PageFactory                               $resultPageFactory
     * @param \Webkul\Helpdesk\Logger\HelpdeskLogger    $helpdeskLogger,
     * @param \Webkul\Helpdesk\Model\CustomerFactory    $customerFactory,
     * @param \Webkul\Helpdesk\Model\ActivityRepository $activityRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger,
        \Webkul\Helpdesk\Model\CustomerFactory $customerFactory,
        \Webkul\Helpdesk\Model\ActivityRepository $activityRepository
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_helpdeskLogger = $helpdeskLogger;
        $this->_customerFactory = $customerFactory;
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
            $helpdeskCustomerId = isset($data['entity_id'])?$data['entity_id']:0;
            if (empty($data)) {
                $this->messageManager->addErrorMessage(__('Unable to find group to save'));
                return $this->resultRedirectFactory->create()->setPath('helpdesk/*/');
                
            }
            if ($helpdeskCustomerId) {
                $model = $this->_customerFactory->create()->load($helpdeskCustomerId);
                $model->setData($data);
                $model->save();
                $this->_activityRepository->saveActivity($helpdeskCustomerId, $model->getName(), "edit", "customer");
            } else {
                $customer = $this->_customerFactory->create()->getCollection();
                $customer->addFieldToFilter('email', $data["email"]);
                $customer->addFieldToFilter('customer_id', $data["customer_id"]);
                if (!$customer->getSize()) {
                    $model = $this->_customerFactory->create();
                    $model->setName($data["name"]);
                    $model->setEmail($data["email"]);
                    $model->setCustomerId($data["customer_id"]);
                    $model->setOrganizations($data["organizations"]);
                    $model->save();
                    $this->_activityRepository->saveActivity($model->getId(), $model->getName(), "add", "customer");
                    $this->messageManager->addSuccessMessage(__("Customer successfully saved"));
                } else {
                    $this->messageManager->addErrorMessage(__("Email is already added with selected customer!!"));
                }
            }
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__("There are some error to save type"));
            $this->_helpdeskLogger->info($e->getMessage());
            return $this->resultRedirectFactory->create()->setPath("*/*/edit", ["id" => $helpdeskCustomerId]);
        }
    }

    /**
     * Check save customer Helpdesk Events Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Helpdesk::customers');
    }
}
