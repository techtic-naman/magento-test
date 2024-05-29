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

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Webkul\Helpdesk\Model\ResourceModel\AgentLevel\CollectionFactory;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * Massactions filter.
     *
     * @var Filter
     */
    private $filter;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Webkul\Helpdesk\Logger\HelpdeskLogger
     */
    private $_logger;

    /**
     * @var \Webkul\Helpdesk\Model\ActivityRepository
     */
    private $_activityRepository;

    /**
     * @param Context                                   $context
     * @param Filter                                    $filter
     * @param CollectionFactory                         $collectionFactory
     * @param \Webkul\Helpdesk\Logger\HelpdeskLogger    $logger
     * @param \Webkul\Helpdesk\Model\ActivityRepository $activityRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \Webkul\Helpdesk\Logger\HelpdeskLogger $logger,
        \Webkul\Helpdesk\Model\ActivityRepository $activityRepository
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->_logger = $logger;
        $this->_activityRepository = $activityRepository;
        parent::__construct($context);
    }

    /**
     * MassDelete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $recordUpdated = 0;
            foreach ($collection as $level) {
                $level->delete();
                $this->_activityRepository->saveActivity($level->getId(), $level->getName(), "delete", "agentlevel");
                $recordUpdated++;
            }
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) were updated.', $recordUpdated));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('There are some error during this action.'));
            $this->_logger->info($e->getMessage());
        }
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }

    /**
     * Check MassAssign Helpdesk Events Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Helpdesk::level');
    }
}
