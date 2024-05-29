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
namespace Webkul\Helpdesk\Controller\Adminhtml\Activity;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Webkul\Helpdesk\Model\ResourceModel\Activity\CollectionFactory;

class Delete extends \Magento\Backend\App\Action
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
     * @param Context                                $context
     * @param Filter                                 $filter
     * @param CollectionFactory                      $collectionFactory
     * @param \Webkul\Helpdesk\Logger\HelpdeskLogger $logger
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \Webkul\Helpdesk\Logger\HelpdeskLogger $logger
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->_logger = $logger;
        parent::__construct($context);
    }

    /**
     * Delete activity
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $recordUpdated = 0;
            foreach ($collection as $activity) {
                $activity->delete();
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
        return $this->_authorization->isAllowed('Webkul_Helpdesk::activity');
    }
}
