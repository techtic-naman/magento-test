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
namespace Webkul\Helpdesk\Controller\Adminhtml\Ticketsmanagement\Tickets;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Checkviewer extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * @var \Webkul\Helpdesk\Logger\HelpdeskLogger
     */
    protected $_helpdeskLogger;

    /**
     * @var \Webkul\Helpdesk\Model\TicketlockFactory
     */
    protected $_ticketlockFactory;

    /**
     * @var \Magento\User\Model\UserFactory
     */
    protected $_userFactory;

    /**
     * @param Context                                  $context
     * @param PageFactory                              $resultPageFactory
     * @param \Webkul\Helpdesk\Model\TicketlockFactory $ticketlockFactory
     * @param \Webkul\Helpdesk\Helper\Data             $helper
     * @param \Webkul\Helpdesk\Logger\HelpdeskLogger   $helpdeskLogger
     * @param \Magento\Backend\Model\Auth\Session      $authSession
     * @param \Magento\User\Model\UserFactory          $userFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Webkul\Helpdesk\Model\TicketlockFactory $ticketlockFactory,
        \Webkul\Helpdesk\Helper\Data $helper,
        \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\User\Model\UserFactory $userFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_ticketlockFactory = $ticketlockFactory;
        $this->_helper = $helper;
        $this->_helpdeskLogger = $helpdeskLogger;
        $this->_authSession = $authSession;
        $this->_userFactory = $userFactory;
    }

    /**
     * Check viewers
     *
     * @return void
     */
    public function execute()
    {
        $lockExpireTime = $this->_helper->getConfigLockExpireTime();
        $time = explode(":", $lockExpireTime);
        $hours = isset($time[0])?$time[0]:"00";
        $minutes = isset($time[1])?$time[1]:"00";
        $seconds = isset($time[2])?$time[2]:"00";
        $time = new \DateTime();
        $time->add(new \DateInterval('PT' . $hours . 'H'));
        $time->add(new \DateInterval('PT' . $minutes . 'M'));
        $time->add(new \DateInterval('PT' . $seconds . 'S'));
        $expiredDate = $time->format('Y-m-d H:i:s');
        $agentId = $this->_authSession->getUser()->getId();
        $ticketId = $this->getRequest()->getParam("ticket_id");
        $ticketLockCollection = $this->_ticketlockFactory->create()->getCollection()
            ->addFieldToFilter("agent_id", $agentId)
            ->addFieldToFilter("ticket_id", $ticketId);
        if (count($ticketLockCollection)) {
            foreach ($ticketLockCollection as $lock) {
                $lock->setLockTime($expiredDate);
                $lock->save();
            }
        } else {
            $ticketLock = $this->_ticketlockFactory->create();
            $ticketLock->setLockTime($expiredDate);
            $ticketLock->setAgentId($agentId);
            $ticketLock->setTicketId($ticketId);
            $ticketLock->save();
        }
        $returnData = [];
        $names = [];
        $count = 0;
        $ticketLockCollection = $this->_ticketlockFactory->create()->getCollection()
            ->addFieldToFilter("agent_id", ["neq"=>$agentId])
            ->addFieldToFilter("ticket_id", $ticketId);
        if (count($ticketLockCollection)) {
            foreach ($ticketLockCollection as $lock) {
                if (strtotime(date('Y-m-d H:i:s')) < strtotime($lock->getLockTime())) {
                    $agent = $this->_userFactory->create()->load($lock->getAgentId());
                    $names[] = $agent->getName();
                    $count++;
                }
            }
        } else {
            $names[] = __("No viewer is watching this ticket");
        }
        $returnData['viewers'] = implode(',', $names);
        $returnData['no'] = $count;
        $this->getResponse()->setHeader('Content-type', 'text/html');
        $this->getResponse()->setBody(json_encode($returnData));
    }

    /**
     * Check checkviewers Helpdesk Events Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Helpdesk::tickets');
    }
}
