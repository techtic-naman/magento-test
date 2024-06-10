<?php
/**
 * Webkul Software.
 *
 * @category Webkul
 * @package  Webkul_Helpdesk
 * @author   Webkul
 * @license  https://store.webkul.com/license.html
 */

namespace Webkul\Helpdesk\Controller\Ticket;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Webkul Marketplace Product Save Controller.
 */
class Submit extends Action implements \Magento\Framework\App\CsrfAwareActionInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $_formKeyValidator;

    /**
     * @param Context                                                            $context
     * @param Session                                                            $customerSession
     * @param FormKeyValidator                                                   $formKeyValidator
     * @param \Webkul\Helpdesk\Helper\Tickets                                    $ticketsHelper
     * @param \Webkul\Helpdesk\Helper\Data                                       $helper
     * @param \Webkul\Helpdesk\Model\TicketsRepository                           $ticketsRepo
     * @param \Webkul\Helpdesk\Model\TicketsFactory                              $ticketsFactory
     * @param \Webkul\Helpdesk\Model\CustomerFactory                             $customerFactory
     * @param \Webkul\Helpdesk\Model\ActivityRepository                          $activityRepo
     * @param \Webkul\Helpdesk\Model\ThreadRepository                            $threadRepo
     * @param \Webkul\Helpdesk\Model\AttachmentRepository                        $attachmentRepo
     * @param \Webkul\Helpdesk\Model\ThreadFactory                               $threadFactory
     * @param \Webkul\Helpdesk\Logger\HelpdeskLogger                             $helpdeskLogger
     * @param \Magento\Customer\Model\CustomerFactory                            $customerDataFactory
     * @param \Magento\Framework\Message\ManagerInterface                        $messageManager
     * @param DataPersistorInterface                                             $dataPersistor
     * @param \Magento\Framework\Escaper                                         $_escaper
     * @param \Webkul\Helpdesk\Model\EventsRepository                            $eventsRepo
     * @param \Webkul\Helpdesk\Model\ResourceModel\Ticketdraft\CollectionFactory $draftCollFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        FormKeyValidator $formKeyValidator,
        \Webkul\Helpdesk\Helper\Tickets $ticketsHelper,
        \Webkul\Helpdesk\Helper\Data $helper,
        \Webkul\Helpdesk\Model\TicketsRepository $ticketsRepo,
        \Webkul\Helpdesk\Model\TicketsFactory $ticketsFactory,
        \Webkul\Helpdesk\Model\CustomerFactory $customerFactory,
        \Webkul\Helpdesk\Model\ActivityRepository $activityRepo,
        \Webkul\Helpdesk\Model\ThreadRepository $threadRepo,
        \Webkul\Helpdesk\Model\AttachmentRepository $attachmentRepo,
        \Webkul\Helpdesk\Model\ThreadFactory $threadFactory,
        \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger,
        \Magento\Customer\Model\CustomerFactory $customerDataFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        DataPersistorInterface $dataPersistor,
        \Magento\Framework\Escaper $_escaper,
        \Webkul\Helpdesk\Model\EventsRepository $eventsRepo,
        \Webkul\Helpdesk\Model\ResourceModel\Ticketdraft\CollectionFactory $draftCollFactory 
    ) {
        $this->_customerSession = $customerSession;
        $this->_formKeyValidator = $formKeyValidator;
        $this->_ticketsHelper = $ticketsHelper;
        $this->_helper = $helper;
        $this->_ticketsRepo = $ticketsRepo;
        $this->_ticketsFactory = $ticketsFactory;
        $this->_customerFactory = $customerFactory;
        $this->_activityRepo = $activityRepo;
        $this->_threadRepo = $threadRepo;
        $this->_attachmentRepo = $attachmentRepo;
        $this->_threadFactory = $threadFactory;
        $this->_helpdeskLogger = $helpdeskLogger;
        $this->customerDataFactory = $customerDataFactory;
        $this->messageManager = $messageManager;
        $this->dataPersistor = $dataPersistor;
        $this->_escaper = $_escaper;
        $this->_eventsRepo = $eventsRepo;
        $this->_draftCollFactory = $draftCollFactory;
        parent::__construct(
            $context
        );
    }

      /**
       * @inheritDoc
       */
    public function createCsrfValidationException(
        RequestInterface $request
    ): ?InvalidRequestException {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * Seller product save action.
     *
     * @return \Magento\Framework\Controller\Result\RedirectFactory
     */
    public function execute()
    {
        try {
            $customer_id = $this->_customerSession->getCustomerId();
            $customerData = $this->customerDataFactory->create()->load($customer_id);
            $userId = $this->_ticketsHelper->getTsCustomerId(true);
            $isloginRequired = $this->_helper->getLoginRequired();
            if ($this->getRequest()->isPost()) {
                $this->dataPersistor->set('new_ticket_form', $this->getRequest()->getPostValue());
                if (!$this->_formKeyValidator->validate($this->getRequest())) {
                    $this->messageManager->addErrorMessage(__("Form key is not valid!!"));
                    return $this->resultRedirectFactory->create()->setPath(
                        '*/*/newticket',
                        ['_secure' => $this->getRequest()->isSecure()]
                    );
                }
                if (!$userId && $isloginRequired) {
                    $this->messageManager->addErrorMessage(__("Please Login !!"));
                    return $this->resultRedirectFactory->create()->setPath('helpdesk/ticket/login/');
                } else {
                    $adminEmail = $this->_helper->getConfigHelpdeskEmail();
                    $wholedata = $this->getRequest()->getParams();
                    $files = $this->getRequest()->getFiles();
                    $wholedata = $this->escapeHtml($wholedata);
                    $wholedata['source'] = "website";
                    $wholedata['who_is'] = "customer";
                    $wholedata['from'] = $wholedata['email'] ?? $customerData['email'];
                    $wholedata['to'] = $adminEmail;
                    if (count($files) > 1) {
                        $wholedata = $this->addCustomAttrFiles($files, $wholedata);
                    }
                    $ticketId = $this->_ticketsRepo->createTicket($wholedata);
                    $senderInfo = ['name'=>$wholedata['fullname'] ?? $customerData
                    ['firstname']." ".$customerData['lastname'],
                    'email'=>$wholedata['email'] ?? $customerData['email'] ];
                    $receiverInfo = ['name'=>'Admin','email'=>$adminEmail];
                    $emailTempVariables['name'] = $receiverInfo['name'];
                    $emailTempVariables['ticket_id'] = $ticketId;
                    $emailTempVariables['customer_name'] = $wholedata['fullname'] ?? $customerData['firstname'].
                    " ".$customerData['lastname'];
                    $emailTempVariables['customer_email'] = $wholedata['email'] ?? $customerData['email'];
                    $template_name = "helpdesk/email/helpdesk_mail";
                    $this->_helper->sendMail(
                        $template_name,
                        $emailTempVariables,
                        $senderInfo,
                        $receiverInfo
                    );
                    $ticket = $this->_ticketsFactory->create()->load($ticketId);
                    $customer = $this->_customerFactory->create()->load($ticket->getCustomerId());
                    $this->_activityRepo->saveActivity($ticketId, $customer->getName(), "add", "ticket");
                    $wholedata['thread_type'] = "create";
                    $threadId = $this->_threadRepo->createThread($ticketId, $wholedata);
                    $this->_eventsRepo->checkTicketEvent("ticket", $ticketId, "created");
                    if (isset($files["fupld"]["tmp_name"][0])) {
                        $this->_attachmentRepo->saveAttachment($ticketId, $threadId);
                    } else {
                        $this->_threadFactory->create()->load($threadId)->setAttachment(0)
                            ->save();
                    }
                    $this->dataPersistor->clear('new_ticket_form');
                    $this->messageManager->addSuccessMessage(
                        __("Your ticket has been created successfully. Your ticket id is : #").$ticketId
                    );
                }
            } else {
                $this->messageManager->addErrorMessage(__("Sorry Nothing Found To Save!!"));
            }
            if ($userId) {
                $draftColl = $this->_draftCollFactory->create()
                        ->addFieldToFilter("ticket_id", 0)
                        ->addFieldToFilter("user_id", $userId)
                        ->addFieldToFilter("user_type", ["eq"=>"customer"])
                        ->addFieldToFilter("field", "new");
                if ($draftColl->getSize()) {
                    $draft = $draftColl->getFirstItem();
                    $draft->delete();
                }
                return $this->resultRedirectFactory->create()->setPath('helpdesk/ticket/mytickets/');

            } else {
                return $this->resultRedirectFactory->create()->setPath('helpdesk/ticket/login/');

            }
        } catch (\Magento\Framework\Exception\InputException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->_helpdeskLogger->info($e->getMessage());
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->_helpdeskLogger->info($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
            $this->_helpdeskLogger->info($e->getMessage());
        }
        return $this->resultRedirectFactory->create()->setPath(
            '*/*/newticket',
            ['_secure' => $this->getRequest()->isSecure()]
        );
    }

    /**
     * EscapeHtml
     *
     * @param array $wholedata
     */
    public function escapeHtml($wholedata)
    {
        foreach ($wholedata as $key => $value) {
            if ($key == "form_key" || $key == "query") {
                continue;
            }
            $wholedata[$key] = $this->_escaper->escapeHtml($value);
        }
        return $wholedata;
    }

    /**
     * AddCustomAttrFiles
     *
     * @param array $CusAttrFiles
     * @param array $wholedata
     * @return array
     */
    public function addCustomAttrFiles($CusAttrFiles, $wholedata)
    {
        
        foreach ($CusAttrFiles as $key => $file) {
            if ($key == 'fupld') {
                continue;
            }
            if (!$file['error'] && $file['size']) {
                $wholedata[$key] = $file['name'];
            }
        }
        return $wholedata;
    }
}
