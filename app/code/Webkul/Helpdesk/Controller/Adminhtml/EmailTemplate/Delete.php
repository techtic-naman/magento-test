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
namespace Webkul\Helpdesk\Controller\Adminhtml\EmailTemplate;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Webkul\Helpdesk\Model\ResourceModel\EmailTemplate\CollectionFactory;

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
     * @var \Webkul\Helpdesk\Model\ActivityRepository
     */
    private $_activityRepo;

    /**
     * @var \Magento\Email\Model\BackendTemplate
     */
    private $_emailbackendTemp;

    /**
     * @var \Webkul\Helpdesk\Model\EmailTemplateFactory
     */
    private $_emailtemplateFactory;

    /**
     * @param Context                                     $context
     * @param Filter                                      $filter
     * @param CollectionFactory                           $collectionFactory
     * @param \Webkul\Helpdesk\Logger\HelpdeskLogger      $logger
     * @param \Webkul\Helpdesk\Model\ActivityRepository   $activityRepo
     * @param \Magento\Email\Model\BackendTemplate        $emailbackendTemp
     * @param \Webkul\Helpdesk\Model\EmailTemplateFactory $emailtemplateFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \Webkul\Helpdesk\Logger\HelpdeskLogger $logger,
        \Webkul\Helpdesk\Model\ActivityRepository $activityRepo,
        \Magento\Email\Model\BackendTemplate $emailbackendTemp,
        \Webkul\Helpdesk\Model\EmailTemplateFactory $emailtemplateFactory
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->_logger = $logger;
        $this->_activityRepo = $activityRepo;
        $this->_emailbackendTemp = $emailbackendTemp;
        $this->_emailtemplateFactory = $emailtemplateFactory;
        parent::__construct($context);
    }

    /**
     * Delete email template
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        try {
            $id = (int)$this->getRequest()->getParam('id');
            $template = $this->_emailbackendTemp->load($id);
            if ($template->getId()) {
                $ticketTemplate = $this->_emailtemplateFactory->create()
                    ->getCollection()
                    ->addFieldToFilter("template_id", ["eq"=>$template->getId()]);
                if (count($ticketTemplate)) {
                    foreach ($ticketTemplate as $row) {
                        $row->delete();
                    }
                }
                $this->_activityRepo->saveActivity($template->getId(), $template->getTemplateCode(), "delete", "email");
                $template->delete();
                $this->messageManager->addSuccessMessage(__('The email template has been deleted.'));
                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }
            $this->messageManager->addErrorMessage(__('Unable to find a Email Template to delete.'));
            return $this->resultRedirectFactory->create()->setPath('*/*/');
            
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
        return $this->_authorization->isAllowed('Webkul_Helpdesk::emailtemplate');
    }
}
