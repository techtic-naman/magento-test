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
namespace Webkul\Helpdesk\Controller\Adminhtml\SupportCenter;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Webkul\Helpdesk\Model\SupportCenterFactory
     */
    protected $_supportCenterFactory;

    /**
     * @var \Webkul\Helpdesk\Logger\HelpdeskLogger
     */
    protected $_helpdeskLogger;

    /**
     * @var \Webkul\Helpdesk\Model\ActivityRepository
     */
    protected $_activityRepo;

    /**
     * @param Context                                     $context
     * @param PageFactory                                 $resultPageFactory
     * @param \Webkul\Helpdesk\Logger\HelpdeskLogger      $helpdeskLogger
     * @param \Webkul\Helpdesk\Model\SupportCenterFactory $supportCenterFactory
     * @param \Webkul\Helpdesk\Model\ActivityRepository   $activityRepo
     * @param \Webkul\Helpdesk\Helper\Data                $helper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger,
        \Webkul\Helpdesk\Model\SupportCenterFactory $supportCenterFactory,
        \Webkul\Helpdesk\Model\ActivityRepository $activityRepo,
        \Webkul\Helpdesk\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_helpdeskLogger = $helpdeskLogger;
        $this->_supportCenterFactory = $supportCenterFactory;
        $this->_activityRepo = $activityRepo;
        $this->_helper = $helper;
    }

    /**
     * Save method
     *
     * @return void
     */
    public function execute()
    {
        try {
            $data = $this->getRequest()->getPostValue();
            $scId = $this->getRequest()->getParam('entity_id');
            if ($this->getRequest()->getPost()) {
                if (is_array($data['cms_id'])) {
                    $data['cms_id'] = implode(",", $data['cms_id']);
                }
                $model = $this->_supportCenterFactory->create();
                $model->setData($data)->setId($scId);
                $model->save();
                if ($scId) {
                    $this->_activityRepo->saveActivity($scId, $model->getName(), "edit", "supportcenter");
                } else {
                    $this->_activityRepo->saveActivity($model->getId(), $model->getName(), "add", "supportcenter");
                }
                $this->_helper->clearCache();
                $this->messageManager->addSuccessMessage(__("Support Center successfully saved"));
            } else {
                $this->messageManager->addErrorMessage(__('Unable to find Support Center to save'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__("There are some error to save Support Center"));
            $this->_helpdeskLogger->info($e->getMessage());
        }
        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }

    /**
     * Check Save action Helpdesk Events Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Helpdesk::supportcenter');
    }
}
