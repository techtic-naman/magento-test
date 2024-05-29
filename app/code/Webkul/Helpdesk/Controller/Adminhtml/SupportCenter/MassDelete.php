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

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Webkul\Helpdesk\Model\ResourceModel\SupportCenter\CollectionFactory;

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
     * @var \Webkul\Helpdesk\Helper\Data
     */
    private $helper;

    /**
     * @param Context                                   $context
     * @param Filter                                    $filter
     * @param CollectionFactory                         $collectionFactory
     * @param \Webkul\Helpdesk\Logger\HelpdeskLogger    $logger
     * @param \Webkul\Helpdesk\Model\ActivityRepository $activityRepository
     * @param \Webkul\Helpdesk\Helper\Data              $helper
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \Webkul\Helpdesk\Logger\HelpdeskLogger $logger,
        \Webkul\Helpdesk\Model\ActivityRepository $activityRepository,
        \Webkul\Helpdesk\Helper\Data $helper
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->_logger = $logger;
        $this->_activityRepository = $activityRepository;
        $this->helper = $helper;
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
            foreach ($collection as $sc) {
                $sc->delete();
                $this->_activityRepository->saveActivity($sc->getId(), $sc->getName(), "delete", "supportcenter");
                $recordUpdated++;
            }
            $this->helper->clearCache();
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) were deleted.', $recordUpdated));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('There are some error during this action.'));
            $this->_logger->info($e->getMessage());
        }
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }

    /**
     * Check MassAssign Helpdesk supportcenter Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Helpdesk::supportcenter');
    }
}
