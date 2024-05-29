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
namespace Webkul\Helpdesk\Controller\Adminhtml\Ticketsmanagement\Attribute;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Webkul\Helpdesk\Model\ResourceModel\TicketsCustomAttributes\CollectionFactory;
use Webkul\Helpdesk\Model\TicketsCustomAttributes;

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
     * @var \Magento\Eav\Model\Entity\AttributeFactory
     */
    private $_eavAttrFactory;

    /**
     * @var \Webkul\Helpdesk\Model\ActivityRepository
     */
    private $_activityRepo;

    /**
     * @param Context                                    $context
     * @param Filter                                     $filter
     * @param CollectionFactory                          $collectionFactory
     * @param \Webkul\Helpdesk\Logger\HelpdeskLogger     $logger
     * @param \Magento\Eav\Model\Entity\AttributeFactory $eavAttrFactory
     * @param \Webkul\Helpdesk\Model\ActivityRepository  $activityRepo
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \Webkul\Helpdesk\Logger\HelpdeskLogger $logger,
        \Magento\Eav\Model\Entity\AttributeFactory $eavAttrFactory,
        \Webkul\Helpdesk\Model\ActivityRepository $activityRepo
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->_logger = $logger;
        $this->_eavAttrFactory = $eavAttrFactory;
        $this->_activityRepo = $activityRepo;
        parent::__construct($context);
    }

    /**
     * Mass delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $recordUpdated = 0;
            foreach ($collection as $customAttr) {
                $attrModel = $this->_eavAttrFactory->create()->load($customAttr->getAttributeId());
                $this->_activityRepo->saveActivity(
                    $customAttr->getAttributeId(),
                    $attrModel->getFrontendLabel(),
                    "delete",
                    "ticketcustomfield"
                );
                $customAttr->delete();
                $attrModel->delete();
                $recordUpdated++;
            }
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) were deleted.', $recordUpdated));
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
        return $this->_authorization->isAllowed('Webkul_Helpdesk::customattribute');
    }
}
