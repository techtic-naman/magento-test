<?php
/**
 * Webkul Software.
 *
 * @category Webkul
 * @package  Webkul_Helpdesk
 * @author   Webkul
 * @license  https://store.webkul.com/license.html
 */
namespace Webkul\Helpdesk\Controller\Adminhtml\Events;

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
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * @var \Magento\User\Model\UserFactory
     */
    protected $_userFactory;

    /**
     * @var \Webkul\Helpdesk\Model\EventsFactory
     */
    protected $_eventsFactory;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_modelSession;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonHelper;

    /**
     * @var \Webkul\Helpdesk\Model\ActivityRepository
     */
    protected $_activityRepo;

    /**
     * @var \Webkul\Helpdesk\Logger\HelpdeskLogger
     */
    protected $_helpdeskLogger;

    /**
     * @param Context                                     $context
     * @param PageFactory                                 $resultPageFactory
     * @param \Magento\Backend\Model\Auth\Session         $authSession
     * @param \Webkul\Helpdesk\Model\EventsFactory        $eventsFactory
     * @param \Magento\Backend\Model\Session              $modelSession
     * @param \Magento\Framework\Json\Helper\Data         $jsonHelper
     * @param \Webkul\Helpdesk\Model\ActivityRepository   $activityRepo
     * @param \Webkul\Helpdesk\Logger\HelpdeskLogger      $helpdeskLogger
     * @param \Magento\Email\Model\BackendTemplateFactory $emailbackendTemp
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Webkul\Helpdesk\Model\EventsFactory $eventsFactory,
        \Magento\Backend\Model\Session $modelSession,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Webkul\Helpdesk\Model\ActivityRepository $activityRepo,
        \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger,
        \Magento\Email\Model\BackendTemplateFactory $emailbackendTemp,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_authSession = $authSession;
        $this->_eventsFactory = $eventsFactory;
        $this->_modelSession = $modelSession;
        $this->_jsonHelper = $jsonHelper;
        $this->_activityRepo = $activityRepo;
        $this->_helpdeskLogger = $helpdeskLogger;
        $this->emailbackendTemp = $emailbackendTemp;
        $this->date = $date;
    }

    /**
     * Save Event
     *
     * @return void
     */
    public function execute()
    {
        try {
            $data = $this->getRequest()->getPostValue();
            $eventId = isset($data['id'])?$data['id']:0;
            if (!$data) {
                $this->resultRedirectFactory->create()->setPath('helpdesk/*/');
                $this->messageManager->addError(__('Unable to find events to save'));
                return;
            }
            $data['event'] = $this->_jsonHelper->jsonEncode(isset($data['event'])?$data['event']:null);
            $data['one_condition_check'] = $this->_jsonHelper->jsonEncode(
                isset(
                    $data
                    ['one_condition']
                )?$data['one_condition']:null
            );
            $data['all_condition_check'] = $this->_jsonHelper->jsonEncode(
                isset(
                    $data
                    ['all_condition']
                )?$data['all_condition']:null
            );
            $data['actions'] = $this->_jsonHelper->jsonEncode(isset($data['action'])?$data['action']:null);
            $type = $data['action']['action-type'][0] ?? "" ;
            $subject = $data['action'][$type]['subject'][0] ?? "No subject";
            if (!empty($type)) {
                if (in_array($type, ['mail_customer','mail_group','mail_agent'])) {
                    $template = $this->emailbackendTemp->create()->getCollection();
                    $template->addFieldToFilter('template_code', $data['action']['action-type'][0]);
                    if ($template->getSize()) {
                        foreach ($template as $temp) {
                            $temp->setTemplateSubject($subject)
                                ->setTemplateCode($data['action']['action-type'][0])
                                ->setTemplateText($data['action'][$type]['content'][0])
                                ->setModifiedAt($this->date->gmtDate());
                            $temp->save();
                        }
                    } else {
                        $template = $this->emailbackendTemp->create();

                        $template->setTemplateSubject($data['action'][$type]['subject'][0])
                            ->setTemplateCode($data['action']['action-type'][0])
                            ->setTemplateText($data['action'][$type]['content'][0])
                            ->setTemplateType(2)
                            ->setModifiedAt($this->date->gmtDate());
                        $template->save();
                    }
                }
            }
            if ($eventId) {
                $model = $this->_eventsFactory->create()->load($eventId);
                $model->setData($data);
                $model->save();
                $this->_activityRepo->saveActivity($eventId, $data['name'], "edit", "eventsandtrigger");
            } else {
                $model = $this->_eventsFactory->create();
                $model->setData($data);
                $model->save();
                $this->_activityRepo->saveActivity($model->getId(), $model->getName(), "add", "eventsandtrigger");
            }
            $this->messageManager->addSuccess(__("Event successfully saved"));
            $this->_modelSession->setFormData(false);
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
            $this->_helpdeskLogger->info($e->getMessage());
            $this->_modelSession->setFormData($data);
            return $this->resultRedirectFactory->create()->setPath("*/*/edit", ["id" => $eventId]);
        }
    }

    /**
     * Check save action Helpdesk Events Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Helpdesk::events');
    }
}
