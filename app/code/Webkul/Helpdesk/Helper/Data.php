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

namespace Webkul\Helpdesk\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Area;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Webkul Helpdesk Helper Data.
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * @var \Magento\Framework\App\Cache\ManagerFactory
     */
    protected $cacheManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var string
     */
    protected $temp_id;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Webkul\Helpdesk\Logger\HelpdeskLogger
     */
    protected $helpdeskLogger;

    /**
     * @param \Magento\Framework\App\Helper\Context               $context
     * @param \Magento\Framework\Filesystem                       $filesystem
     * @param \Magento\Store\Model\StoreManagerInterface          $storeManager
     * @param \Magento\Backend\Model\Auth\Session                 $authSession
     * @param \Magento\Framework\App\State                        $appState
     * @param \Magento\Framework\Mail\Template\TransportBuilder   $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface  $inlineTranslation
     * @param DataPersistorInterface                              $dataPersistor
     * @param \Magento\Framework\App\Cache\ManagerFactory         $cacheManagerFactory
     * @param \Magento\Framework\Message\ManagerInterface         $messageManager
     * @param \Webkul\Helpdesk\Logger\HelpdeskLogger              $helpdeskLogger
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        DataPersistorInterface $dataPersistor,
        \Magento\Framework\App\Cache\ManagerFactory $cacheManagerFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger
    ) {
        parent::__construct($context);
        $this->_filesystem = $filesystem;
        $this->_storeManager = $storeManager;
        $this->_authSession = $authSession;
        $this->_appState = $appState;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->dataPersistor = $dataPersistor;
        $this->cacheManager = $cacheManagerFactory;
        $this->messageManager = $messageManager;
        $this->helpdeskLogger = $helpdeskLogger;
    }

    /**
     * Get Persistent Data for Product
     *
     * @return array
     */
    public function getPersistentData()
    {
        $persistorData = $this->dataPersistor->get('new_ticket_form');
        if ($persistorData == null) {
             $persistorData = [
                 'fullname' => '',
                 'email'     => '',
                 'subject'   => '',
                 'query'     => ''
             ];
        }

        return $persistorData;
    }

    /**
     * Check agent can see ticket
     *
     * @return int
     */

    public function canAgentSeeTickets()
    {
        return $this->scopeConfig->getValue(
            'helpdesk/helpdesk_settings/agent_ticket_visibilty',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check ticket status
     *
     * @return int
     */

    public function getTicketDefaultStatus()
    {
        return $this->scopeConfig->getValue(
            'helpdesk/ticketdeafult/defaultstatus',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check ticket group
     *
     * @return int
     */

    public function getTicketDefaultGroup()
    {
        return $this->scopeConfig->getValue(
            'helpdesk/ticketdeafult/defaultgroup',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check ticket priority
     *
     * @return int
     */

    public function getTicketDefaultPriority()
    {
        return $this->scopeConfig->getValue(
            'helpdesk/ticketdeafult/defaultpriority',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check ticket type
     *
     * @return int
     */

    public function getTicketDefaultType()
    {
        return $this->scopeConfig->getValue(
            'helpdesk/ticketdeafult/defaulttype',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get allowed activities
     *
     * @return array
     */

    public function getAllowedActivity()
    {
        return $this->scopeConfig->getValue(
            'helpdesk/activity/allowedactivity',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get activity priroity on edit
     *
     * @return string
     */
    public function getActivityPriorityEdit()
    {
        return $this->scopeConfig->getValue(
            'helpdesk/activity/activitypriorityonedit',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get activity priroity on Add
     *
     * @return string
     */
    public function getActivityPriorityAdd()
    {
        return $this->scopeConfig->getValue(
            'helpdesk/activity/activitypriorityonadd',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get activity priroity on Delete
     *
     * @return string
     */
    public function getActivityPriorityDelete()
    {
        return $this->scopeConfig->getValue(
            'helpdesk/activity/activitypriorityondelete',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Date format
     *
     * @return string
     */
    public function getDateFormate()
    {
        return $this->scopeConfig->getValue(
            'helpdesk/helpdesk_settings/dateformat',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Config thread limit setting
     *
     * @return int
     */
    public function getThreadLimit()
    {
        return $this->scopeConfig->getValue(
            'helpdesk/admin_ticketview/threadlimit',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get login required setting
     *
     * @return int
     */
    public function getLoginRequired()
    {
        return $this->scopeConfig->getValue(
            'helpdesk/helpdesk_settings/loginrequired',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get dashboard activity limit
     *
     * @return int
     */
    public function getDashboardActivityLimit()
    {
        return $this->scopeConfig->getValue(
            'helpdesk/activity/dashboardactivitylimit',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get ticket creation priority
     *
     * @return int
     */
    public function getConfigTicketCreationPriority()
    {
        return $this->scopeConfig->getValue(
            'helpdesk/helpdesk_settings/priority_on_creation',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get ticket creation group
     *
     * @return int
     */
    public function getConfigTicketCreationGroup()
    {
        return $this->scopeConfig->getValue(
            'helpdesk/helpdesk_settings/group_on_creation',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get ticket creation status
     *
     * @return int
     */
    public function getConfigTicketCreationStatus()
    {
        return $this->scopeConfig->getValue(
            'helpdesk/helpdesk_settings/status_on_creation',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Media Path
     *
     * @return string
     */
    public function getMediaPath()
    {
        return $this->_filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        )->getAbsolutePath();
    }

    /**
     * Get Media Url
     *
     * @return string
     */
    public function getMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * Get Default Wesite Id
     *
     * @return int
     */
    public function getDefaultWebsiteId()
    {
        return $this->_storeManager->getStore()->getWebsiteId();
    }

    /**
     * Get Helpdesk name
     *
     * @return string
     */
    public function getConfigHelpdeskName()
    {
        return $this->scopeConfig->getValue(
            'helpdesk/helpdesk_settings/ticketsystemname',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Helpdesk email
     *
     * @return string
     */
    public function getConfigHelpdeskEmail()
    {
        return $this->scopeConfig->getValue(
            'helpdesk/helpdesk_settings/ticketsystememail',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Draft save time
     *
     * @return int
     */
    public function getConfigDraftsavetime()
    {
        return $this->scopeConfig->getValue(
            'helpdesk/admin_ticketview/draftsavetime',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Lock view time
     *
     * @return int
     */
    public function getConfigLockviewtime()
    {
        return $this->scopeConfig->getValue(
            'helpdesk/admin_ticketview/lockviewtime',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get allowed extension
     *
     * @return string
     */
    public function getConfigAllowedextensions()
    {
        return $this->scopeConfig->getValue(
            'helpdesk/helpdesk_settings/allowedextensions',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check WYSIWYG editor enabled/disabled
     *
     * @return int
     */
    public function getConfigAllowEditor()
    {
        return $this->scopeConfig->getValue(
            'helpdesk/helpdesk_settings/alloweditor',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get spam status on ticket
     *
     * @return int
     */
    public function getConfigSpamstatus()
    {
        return $this->scopeConfig->getValue(
            'helpdesk/ticketstatus/spamstatus',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get spam status on ticket
     *
     * @return int
     */
    public function getConfigNumAllowFile()
    {
        return $this->scopeConfig->getValue(
            'helpdesk/ticketstatus/spamstatus',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get lock expire time
     *
     * @return int
     */
    public function getConfigLockExpireTime()
    {
        return $this->scopeConfig->getValue(
            'helpdesk/admin_ticketview/lockexpiretime',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get close status of ticket
     *
     * @return int
     */
    public function getConfigCloseStatus()
    {
        return $this->scopeConfig->getValue(
            'helpdesk/ticketstatus/closedstatus',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get resove status of ticket
     *
     * @return int
     */
    public function getConfigResolveStatus()
    {
        return $this->scopeConfig->getValue(
            'helpdesk/ticketstatus/solvestatus',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get pening status of ticket
     *
     * @return int
     */
    public function getConfigPendingStatus()
    {
        return $this->scopeConfig->getValue(
            'helpdesk/ticketstatus/pendingstatus',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get new status on ticket
     *
     * @return int
     */
    public function getConfigNewStatus()
    {
        return $this->scopeConfig->getValue(
            'helpdesk/ticketstatus/newstatus',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get spam new on ticket
     *
     * @return int
     */
    public function getConfigOpenStatus()
    {
        return $this->scopeConfig->getValue(
            'helpdesk/ticketstatus/openstatus',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get upload file size
     *
     * @return int
     */
    public function getUploadFileSize()
    {
        return $this->scopeConfig->getValue(
            'helpdesk/helpdesk_settings/uploadfilesize',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check to delete ticket from customer end
     *
     * @return int
     */
    public function getConfigCustomerDeleteTicket()
    {
        return $this->scopeConfig->getValue(
            'helpdesk/helpdesk_settings/customer_can_delete_ticket',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check to delete thread from customer end
     *
     * @return int
     */
    public function getConfigCustomerDeleteThread()
    {
        return $this->scopeConfig->getValue(
            'helpdesk/helpdesk_settings/customer_can_delete_thread',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
 
    /**
     * Check to close ticket from customer end
     *
     * @return int
     */
    public function getConfigCustomerCloseTicket()
    {
        return $this->scopeConfig->getValue(
            'helpdesk/helpdesk_settings/customer_can_close_ticket',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check to add CC in mail from customer end
     *
     * @return int
     */
    public function getConfigCustomerCanAddCc()
    {
        return $this->scopeConfig->getValue(
            'helpdesk/helpdesk_settings/customer_can_add_cc',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Return store configuration value.
     *
     * @param string $path
     * @param int    $storeId
     *
     * @return mixed
     */
    protected function getConfigValue($path, $storeId)
    {
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if access of controller
     *
     * @param string $action
     * @return boolean
     */
    public function getPermission($action)
    {
        return $this->_authSession->isAllowed($action);
    }

    /**
     * Check is Admin or customer
     *
     * @return boolean
     */
    public function isAdmin()
    {
        $isAdmin = false;
        if ($this->_appState->getAreaCode() == Area::AREA_ADMINHTML) {
            $isAdmin = true;
        }
        return $isAdmin;
    }

    /**
     * Send mail
     *
     * @param mixed $template_name
     * @param array $emailTempVariables
     * @param array $senderInfo
     * @param array $receiverInfo
     * @return boolean
     */
    public function sendMail(
        $template_name,
        $emailTempVariables,
        $senderInfo,
        $receiverInfo
    ) {
        try {
            $this->temp_id = $this->getTemplateId($template_name);
            $this->inlineTranslation->suspend();
            $this->generateTemplate($emailTempVariables, $senderInfo, $receiverInfo);
            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->helpdeskLogger->info($e->getMessage());
            $this->messageManager->addErrorMessage(__($e->getMessage()));
        }
    }

    /**
     * Get Template Id
     *
     * @param mixed $xmlPath
     * @return int
     */
    public function getTemplateId($xmlPath)
    {
        return $this->getConfigValue($xmlPath, $this->getStore()->getStoreId());
    }

    /**
     * Generate Template For Mail
     *
     * @param array $emailTemplateVariables
     * @param array $senderInfo
     * @param array $receiverInfo
     * @return boolean
     */
    public function generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $template =  $this->transportBuilder->setTemplateIdentifier($this->temp_id)
            ->setTemplateOptions(
                [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $this->_storeManager->getStore()->getId(),
                    ]
            )
            ->setTemplateVars($emailTemplateVariables)
            ->setFrom($senderInfo);
        if (is_array($receiverInfo['email'])) {
            foreach ($receiverInfo['email'] as $receiver) {
                $template->addTo($receiver, $receiverInfo['name']);
            }
            if (isset($receiverInfo['cc']) && count($receiverInfo['cc'])) {
                foreach ($receiverInfo['cc'] as $cc) {
                    $template->addCc($cc);
                }
            }
            if (isset($receiverInfo['bcc']) && count($receiverInfo['bcc'])) {
                $template->addBcc($receiverInfo['bcc']);
                foreach ($receiverInfo['bcc'] as $bcc) {
                    $template->addBcc($bcc);
                }
            }
        } else {
            $template->addTo($receiverInfo['email'], $receiverInfo['name']);
            if (isset($receiverInfo['cc']) && count($receiverInfo['cc'])) {
                foreach ($receiverInfo['cc'] as $cc) {
                    $template->addCc($cc);
                }
            }
            if (isset($receiverInfo['bcc']) && count($receiverInfo['bcc'])) {
                foreach ($receiverInfo['cc'] as $cc) {
                    $template->addBcc($cc);
                }
            }
        }
        
        return $this;
    }
    /**
     * Get store
     *
     * @return int
     */
    public function getStore()
    {
        return $this->_storeManager->getStore();
    }

    /**
     * Clean Cache
     */
    public function clearCache()
    {
        $cacheManager = $this->cacheManager->create();
        $availableTypes = $cacheManager->getAvailableTypes();
        $cacheManager->clean($availableTypes);
    }
}
