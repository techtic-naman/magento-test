<?php
/**
 * Webkul Software.
 *
 * @category Webkul
 * @package  Webkul_Helpdesk
 * @author   Webkul
 * @license  https://store.webkul.com/license.html
 */
namespace Webkul\Helpdesk\Controller\Adminhtml\EmailTemplate;

use Magento\Framework\Exception\AuthenticationException;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Email\Model\Template;

class Preview extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Preview transactional email action
     *
     * @return void
     */
    public function execute()
    {
        try {
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->prepend(__('Email Preview'));
            $this->getResponse()->setHeader('Content-Security-Policy', "script-src 'none'");
            return $resultPage;
        } catch (\Exception $e) {
            $this->messageManager->addError(__('An error occurred. The email template can not be opened for preview.'));
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }
    }

    /**
     * Check Email template Helpdesk Events Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Helpdesk::emailtemplate');
    }
}
