<?php
/**
 * Webkul Software.
 *
 * @category Webkul
 * @package  Webkul_Helpdesk
 * @author   Webkul
 * @license  https://store.webkul.com/license.html
 */
namespace Webkul\Helpdesk\Controller\Adminhtml\Ticketsmanagement\Priority;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Webkul\Helpdesk\Model\ResourceModel\TicketsPriority\CollectionFactory;

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
     * @param Context                                   $context
     * @param Filter                                    $filter
     * @param CollectionFactory                         $collectionFactory
     * @param \Webkul\Helpdesk\Logger\HelpdeskLogger    $logger
     * @param \Webkul\Helpdesk\Model\ActivityRepository $activityRepo
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \Webkul\Helpdesk\Logger\HelpdeskLogger $logger,
        \Webkul\Helpdesk\Model\ActivityRepository $activityRepo
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->_logger = $logger;
        $this->_activityRepo = $activityRepo;
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
            foreach ($collection as $priority) {
                $priority->delete();
                $this->_activityRepo->saveActivity(
                    $priority->getEntityId(),
                    $priority->getName(),
                    "delete",
                    "priority"
                );
                $recordUpdated++;
            }
            $this->messageManager->addSuccess(__('A total of %1 record(s) were deleted.', $recordUpdated));
        } catch (\Exception $e) {
            $this->messageManager->addError(__('There are some error during this action.'));
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
        return $this->_authorization->isAllowed('Webkul_Helpdesk::priority');
    }
}
