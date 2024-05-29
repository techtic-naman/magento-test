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
namespace Webkul\Helpdesk\Controller\Adminhtml\ConnectEmail;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_modelSession;

    /**
     * @var \Webkul\Helpdesk\Model\ActivityRepository
     */
    protected $_activityRepo;

    /**
     * @var \Webkul\Helpdesk\Logger\HelpdeskLogger
     */
    protected $_helpdeskLogger;

    /**
     * @var \Webkul\Helpdesk\Model\ConnectEmailFactory
     */
    protected $_connectEmailFactory;

    /**
     * @var \Webkul\Helpdesk\Helper\Tickets
     */
    protected $helper;

    /**
     * @param Context                                    $context
     * @param PageFactory                                $resultPageFactory
     * @param \Webkul\Helpdesk\Model\ConnectEmailFactory $connectEmailFactory
     * @param \Magento\Backend\Model\Session             $modelSession
     * @param \Webkul\Helpdesk\Model\ActivityRepository  $activityRepo
     * @param \Webkul\Helpdesk\Logger\HelpdeskLogger     $helpdeskLogger
     * @param \Webkul\Helpdesk\Helper\Tickets            $helper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Webkul\Helpdesk\Model\ConnectEmailFactory $connectEmailFactory,
        \Magento\Backend\Model\Session $modelSession,
        \Webkul\Helpdesk\Model\ActivityRepository $activityRepo,
        \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger,
        \Webkul\Helpdesk\Helper\Tickets $helper
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_connectEmailFactory = $connectEmailFactory;
        $this->_modelSession = $modelSession;
        $this->_activityRepo = $activityRepo;
        $this->_helpdeskLogger = $helpdeskLogger;
        $this->helper = $helper;
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
            $cEmailId = $this->getRequest()->getParam('id');
            if ($this->getRequest()->getPost()) {
                $cEmailModel = $this->_connectEmailFactory->create();
                if (empty($data['count'])) {
                    $data['count'] = 0;
                }
                $flag = true;
                if ($cEmailId) {
                    $cEmailModel = $cEmailModel->load($cEmailId);
                    $passwordDb = $cEmailModel->getPassword();
                    if ($passwordDb == $data['password']) {
                        $flag = false;
                    }
                }
                if ($flag) {
                    $data['password'] = $this->helper->encryptData($data['password']);
                }
                $cEmailModel->setData($data)->setId($cEmailId);
                $cEmailModel->save();
                if ($cEmailId) {
                    $this->_activityRepo->saveActivity($cEmailId, $cEmailModel->getName(), "edit", "connectemail");
                } else {
                    $this->_activityRepo->saveActivity(
                        $cEmailModel->getId(),
                        $cEmailModel->getName(),
                        "add",
                        "connectemail"
                    );
                }
                $this->messageManager->addSuccessMessage(__("Item was successfully saved"));
                $this->_modelSession->setFormData(false);
            }
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__("There are some error to save event"));
            $this->_helpdeskLogger->info($e->getMessage());
            $this->_modelSession->setFormData($data);
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }
    }

    /**
     * Check Save action Helpdesk Connect Email Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Helpdesk::connectemail');
    }
}
