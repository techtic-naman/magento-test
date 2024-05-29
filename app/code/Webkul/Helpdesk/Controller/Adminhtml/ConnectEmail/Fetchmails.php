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

class Fetchmails extends \Magento\Backend\App\Action
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
     * @var \Webkul\Helpdesk\Model\MailfetchRepository
     */
    protected $_mailfetchRepo;

    /**
     * @param Context                                    $context
     * @param PageFactory                                $resultPageFactory
     * @param \Webkul\Helpdesk\Model\ConnectEmailFactory $connectEmailFactory
     * @param \Magento\Backend\Model\Session             $modelSession
     * @param \Webkul\Helpdesk\Model\ActivityRepository  $activityRepo
     * @param \Webkul\Helpdesk\Logger\HelpdeskLogger     $helpdeskLogger
     * @param \Webkul\Helpdesk\Model\MailfetchRepository $mailfetchRepo
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Webkul\Helpdesk\Model\ConnectEmailFactory $connectEmailFactory,
        \Magento\Backend\Model\Session $modelSession,
        \Webkul\Helpdesk\Model\ActivityRepository $activityRepo,
        \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger,
        \Webkul\Helpdesk\Model\MailfetchRepository $mailfetchRepo
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_connectEmailFactory = $connectEmailFactory;
        $this->_modelSession = $modelSession;
        $this->_activityRepo = $activityRepo;
        $this->_helpdeskLogger = $helpdeskLogger;
        $this->_mailfetchRepo = $mailfetchRepo;
    }

    /**
     * Execute method
     *
     * @return void
     */
    public function execute()
    {
        try {
            $cEmailId = $this->getRequest()->getParam('id');
            if ($cEmailId) {
                $count = $this->_mailfetchRepo->fetchMail($cEmailId);
                $this->messageManager->addSuccessMessage(__("%1 email was successfully fetched.", $count));
                return  $this->resultRedirectFactory->create()->setPath("*/*/edit", ["id" => $cEmailId]);
               
            } else {
                $this->messageManager->addErrorMessage(__("Item does not exist"));
                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
            return $this->resultRedirectFactory->create()->setPath("*/*/edit", ["id" => $cEmailId]);
        }
    }

    /**
     * Check fetchemail Helpdesk Events Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Helpdesk::connectemail');
    }
}
