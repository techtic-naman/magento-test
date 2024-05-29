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
namespace Webkul\Helpdesk\Controller\Adminhtml\Agentsmanagement\Level;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Webkul\Helpdesk\Model\AgentLevelFactory
     */
    protected $_agentLevelFactory;

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
     * @param \Webkul\Helpdesk\Model\AgentLevelFactory  $agentLevelFactory,
     * @param \Webkul\Helpdesk\Model\ActivityRepository $activityRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger,
        \Webkul\Helpdesk\Model\AgentLevelFactory $agentLevelFactory,
        \Webkul\Helpdesk\Model\ActivityRepository $activityRepository
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_helpdeskLogger = $helpdeskLogger;
        $this->_agentLevelFactory = $agentLevelFactory;
        $this->_activityRepository = $activityRepository;
    }

    /**
     * Save Level
     *
     * @return void
     */
    public function execute()
    {
        try {
            $data = $this->getRequest()->getPostValue();
            $levelId = isset($data['entity_id'])?$data['entity_id']:0;
            if (empty($data)) {
                $this->messageManager->addErrorMessage(__('Unable to find level to save'));
                return $this->resultRedirectFactory->create()->setPath('helpdesk/*/');
            }
            if ($levelId) {
                $model = $this->_agentLevelFactory->create()->load($levelId);
                $model->setData($data);
                $model->save();
                $this->_activityRepository->saveActivity($levelId, $model->getName(), "edit", "agentlevel");
            } else {
                $model = $this->_agentLevelFactory->create();
                $model->setName($data["name"]);
                $model->setDescription($data["description"]);
                $model->setStatus($data["status"]);
                $model->save();
                $this->_activityRepository->saveActivity($model->getId(), $model->getName(), "add", "agentlevel");
            }
            $this->messageManager->addSuccessMessage(__("Level successfully saved"));
            return $this->resultRedirectFactory->create()->setPath('helpdesk/*/');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__("There are some error to save type"));
            $this->_helpdeskLogger->info($e->getMessage());
            return $this->resultRedirectFactory->create()->setPath("*/*/edit", ["id" => $levelId]);

        }
    }

    /**
     * Check Save action Helpdesk Events Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Helpdesk::level');
    }
}
