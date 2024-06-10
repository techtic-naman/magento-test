<?php
/**
 * Webkul Software.
 *
 * @category Webkul
 * @package  Webkul_Helpdesk
 * @author   Webkul
 * @license  https://store.webkul.com/license.html
 */
namespace Webkul\Helpdesk\Controller\Adminhtml\Businesshours;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Webkul\Helpdesk\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Webkul\Helpdesk\Logger\HelpdeskLogger
     */
    protected $_helpdeskLogger;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_modelSession;

    /**
     * @param Context                                          $context
     * @param PageFactory                                      $resultPageFactory
     * @param \Webkul\Helpdesk\Logger\HelpdeskLogger           $helpdeskLogger
     * @param \Webkul\Helpdesk\Model\BusinesshoursFactory      $businesshourFactory
     * @param \Webkul\Helpdesk\Model\ActivityRepository        $activityRepository
     * @param \Webkul\Helpdesk\Model\Source\Days               $daysOpt
     * @param \Magento\Backend\Model\Session                   $modelSession
     * @param \Magento\Framework\Serialize\SerializerInterface $serialize
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger,
        \Webkul\Helpdesk\Model\BusinesshoursFactory $businesshourFactory,
        \Webkul\Helpdesk\Model\ActivityRepository $activityRepository,
        \Webkul\Helpdesk\Model\Source\Days $daysOpt,
        \Magento\Backend\Model\Session $modelSession,
        \Magento\Framework\Serialize\SerializerInterface $serialize
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_helpdeskLogger = $helpdeskLogger;
        $this->_businesshourFactory = $businesshourFactory;
        $this->_activityRepo = $activityRepository;
        $this->_daysOpt = $daysOpt;
        $this->_modelSession = $modelSession;
        $this->serialize = $serialize;
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
            $businesshourId = isset($data['entity_id'])?$data['entity_id']:0;
            $days = $this->_daysOpt->toOptionArray();
            $helpdeskHours = [];
            foreach ($days as $key => $day) {
                if (isset($data['business_hours'][$day])) {
                    $diff = strtotime(
                        '2009-10-05 '.$data['business_hours']['evening_'.$day]
                        .' '.$data['business_hours']['evening_cycle_'.$day]
                    ) - strtotime(
                        '2009-10-05 '.$data['business_hours']['morning_'.$day].' '.$data
                            ['business_hours']['morning_cycle_'.$day]
                    );
                    if ($diff > 0) {
                        $helpdeskHours[$day] = ['morning_'.$day => $data['business_hours']
                        ['morning_'.$day],'morning_cycle_'.$day => $data['business_hours']
                        ['morning_cycle_'.$day],'evening_'.$day => $data['business_hours']
                        ['evening_'.$day],'evening_cycle_'.$day => $data['business_hours']
                        ['evening_cycle_'.$day]];
                    }
                }
            }
            $data['helpdesk_hours'] = $this->serialize->serialize($helpdeskHours);
            $hollydayList = [];
            if (isset($data['holiday'])) {
                foreach ($data['holiday']['name'] as $key => $holiday) {
                    $hollydayList[] = ["month" => $data['holiday']['month'][$key], "day" =>
                    $data['holiday']['day'][$key],"name" =>$holiday];
                }
            }
            $data['hollyday_list'] = $this->serialize->serialize($hollydayList);
            if ($businesshourId) {
                $model = $this->_businesshourFactory->create()->load($businesshourId);
                $model->setData($data);
                $model->save();
                $this->_activityRepo->saveActivity(
                    $businesshourId,
                    $model->getBusinesshourName(),
                    "edit",
                    "businesshour"
                );
            } else {
                $model = $this->_businesshourFactory->create();
                $model->setBusinesshourName($data["businesshour_name"]);
                $model->setDescription($data["description"]);
                $model->setTimezone($data["timezone"]);
                $model->setHelpdeskHours($data["helpdesk_hours"]);
                $model->setHollydayList($data["hollyday_list"]);
                $model->save();
                $this->_activityRepo->saveActivity(
                    $model->getId(),
                    $model->getBusinesshourName(),
                    "add",
                    "businesshour"
                );
            }
            $this->messageManager->addSuccessMessage(__("Business hour successfully saved"));
            $this->_modelSession->setFormData(false);
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__("There are some error to save type"));
            $this->_modelSession->setFormData($data);
            $this->_helpdeskLogger->info($e->getMessage());
            return $this->resultRedirectFactory->create()->setPath('*/*/edit', ["id"=>$businesshourId]);
        }
    }

    /**
     * Check for save action permission
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Helpdesk::businesshours');
    }
}
