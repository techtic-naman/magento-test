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
namespace Webkul\Helpdesk\Controller\Ticket;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Webkul Marketplace Landing page Index Controller.
 */
class Logout extends Action
{
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Magento\Framework\Session\SessionManager
     */
    protected $_coreSession;

    /**
     * @param Context                                   $context
     * @param PageFactory                               $resultPageFactory
     * @param \Magento\Framework\Session\SessionManager $coreSession
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Session\SessionManager $coreSession
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_coreSession = $coreSession;
        parent::__construct($context);
    }

    /**
     * Helpdesk Support page.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $this->_coreSession->unsTsCustomer();
        $this->messageManager->addSuccessMessage(__("You are successfully Logged Out"));
        return $this->resultRedirectFactory->create()->setPath('helpdesk/ticket/');
    }
}
