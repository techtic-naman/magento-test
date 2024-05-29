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

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * @var \Magento\User\Model\UserFactory
     */
    protected $_userFactory;

    /**
     * @var \Webkul\Helpdesk\Model\SlapolicyFactory
     */
    protected $_slapolicyFactory;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_modelSession;

    /**
     * @var \Webkul\Helpdesk\Model\ResourceModel\SlapolicyFactory
     */
    protected $resSlaPolicyFactory;

    /**
     * @var \Webkul\Helpdesk\Model\ActivityRepository
     */
    protected $_activityRepo;

    /**
     * @var \Webkul\Helpdesk\Logger\HelpdeskLogger
     */
    protected $_helpdeskLogger;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * @param Context                                               $context
     * @param PageFactory                                           $resultPageFactory
     * @param \Magento\Backend\Model\Auth\Session                   $authSession
     * @param \Webkul\Helpdesk\Model\SlapolicyFactory               $slapolicyFactory
     * @param \Magento\Backend\Model\Session                        $modelSession
     * @param \Webkul\Helpdesk\Logger\HelpdeskLogger                $helpdeskLogger
     * @param \Magento\Framework\Serialize\SerializerInterface      $serializer
     * @param \Webkul\Helpdesk\Model\ResourceModel\SlapolicyFactory $resSlaPolicyFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Webkul\Helpdesk\Model\SlapolicyFactory $slapolicyFactory,
        \Magento\Backend\Model\Session $modelSession,
        \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Webkul\Helpdesk\Model\ResourceModel\SlapolicyFactory $resSlaPolicyFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_authSession = $authSession;
        $this->_slapolicyFactory = $slapolicyFactory;
        $this->_modelSession = $modelSession;
        $this->_helpdeskLogger = $helpdeskLogger;
        $this->serializer = $serializer;
        $this->resSlaPolicyFactory = $resSlaPolicyFactory;
    }

    /**
     * Save Slapolicy
     *
     * @return void
     */
    public function execute()
    {
        try {
            $data = $this->getRequest()->getPostValue();
            $slaId = isset($data['id']) ? $data['id'] : 0;
            if (!array_key_exists('one_condition', $data) && !array_key_exists('all_condition', $data)) {
                $this->messageManager->addErrorMessage(__("Please select condition for SLA!"));
                return $this->resultRedirectFactory->create()->setPath('helpdesk/*/');

            }
            if (!$data) {
                $this->messageManager->addErrorMessage(__('Unable to find events to save'));
                return $this->resultRedirectFactory->create()->setPath('helpdesk/*/');
            }
            
            $data['one_condition_check'] = $this->serializer->serialize(
                isset(
                    $data
                    ['one_condition']
                )?$data['one_condition']:null
            );
            $data['all_condition_check'] = $this->serializer->serialize(
                isset(
                    $data
                    ['all_condition']
                )?$data['all_condition']:null
            );
            $data['sla_service_level_targets'] = $this->serializer->serialize($data['sla']);
            $resSlaPolicy = $this->resSlaPolicyFactory->create();
            if ($slaId) {
                $model = $this->_slapolicyFactory->create()->load($slaId);
                $model->setData($data);
                $resSlaPolicy->save($model);
            } else {
                $model = $this->_slapolicyFactory->create();
                $model->setData($data);
                $resSlaPolicy->save($model);
            }
            $this->messageManager->addSuccessMessage(__("SLA Policy successfully saved"));
            $this->_modelSession->setFormData(false);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__("There are some error to save event"));
            $this->_helpdeskLogger->info($e->getMessage());
            $this->_modelSession->setFormData($data);
        }
        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }

    /**
     * Check Save Helpdesk Events Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Helpdesk::sla');
    }
}
