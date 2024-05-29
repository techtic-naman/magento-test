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
namespace Webkul\Helpdesk\Controller\Adminhtml\Responses;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Applyresponse extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Webkul\Helpdesk\Logger\HelpdeskLogger
     */
    protected $_helpdeskLogger;

    /**
     * @var \Webkul\Helpdesk\Model\ResponsesFactory
     */
    protected $responsesFactory;

    /**
     * @var \Webkul\Helpdesk\Model\ResponsesFactory
     */
    protected $responsesRepo;

    /**
     * @param Context                                    $context
     * @param PageFactory                                $resultPageFactory
     * @param \Webkul\Helpdesk\Model\ResponsesFactory    $responseFactory
     * @param \Webkul\Helpdesk\Model\ResponsesRepository $responseRepo
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Webkul\Helpdesk\Model\ResponsesFactory $responseFactory,
        \Webkul\Helpdesk\Model\ResponsesRepository $responseRepo
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->responsesFactory = $responseFactory;
        $this->responsesRepo = $responseRepo;
    }

    /**
     * Apply response
     *
     * @return void
     */
    public function execute()
    {
        $ticketId = (int)$this->getRequest()->getParam("id");
        $responseId = (int)$this->getRequest()->getParam("responseid");
        $responseModel = $this->responsesFactory->create()->load($responseId);
        $this->responsesRepo->applyResponseToTicket($ticketId, $responseModel->getActions());
        $this->messageManager->addSuccessMessage(__("Response has been successfully applied."));
        return $this->resultRedirectFactory
        ->create()
        ->setPath('helpdesk/ticketsmanagement_tickets/viewreply', ["id"=>$ticketId]);
    }
    
    /**
     * Check applyresponse action Helpdesk Events Permission.
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Helpdesk::responses');
    }
}
