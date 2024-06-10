<?php
/**
 * Webkul Software.
 *
 * @category Webkul
 * @package  Webkul_Helpdesk
 * @author   Webkul
 * @license  https://store.webkul.com/license.html
 */
namespace Webkul\Helpdesk\Model;

use \Magento\Framework\Exception\CouldNotSaveException;

class MailfetchRepository implements \Webkul\Helpdesk\Api\MailfetchRepositoryInterface
{
    /**
     * @var \Webkul\Helpdesk\Helper\Data
     */
    protected $_connectemailFactory;

    /**
     * @var \Webkul\Helpdesk\Logger\HelpdeskLogger
     */
    protected $_helpdeskLogger;
    public const TABLENAME = "helpdesk_ticket_mail_details";

    /**
     * TicketsRepository constructor.
     *
     * @param \Webkul\Helpdesk\Model\ConnectEmailFactory $connectemailFactory
     * @param \Webkul\Helpdesk\Model\MailfetchFactory    $mailfetchFactory
     * @param \Webkul\Helpdesk\Model\ThreadRepository    $threadRepo
     * @param \Webkul\Helpdesk\Model\TicketsRepository   $ticketsRepo
     * @param \Webkul\Helpdesk\Model\EventsRepository    $eventsRepo
     * @param \Webkul\Helpdesk\Logger\HelpdeskLogger     $helpdeskLogger
     * @param \Magento\Framework\App\ResourceConnection  $resource
     * @param \Webkul\Helpdesk\Helper\Tickets            $ticketsHelper
     * @param \Webkul\Helpdesk\Model\TicketsFactory      $ticketsFactory
     */
    public function __construct(
        \Webkul\Helpdesk\Model\ConnectEmailFactory $connectemailFactory,
        \Webkul\Helpdesk\Model\MailfetchFactory $mailfetchFactory,
        \Webkul\Helpdesk\Model\ThreadRepository $threadRepo,
        \Webkul\Helpdesk\Model\TicketsRepository $ticketsRepo,
        \Webkul\Helpdesk\Model\EventsRepository $eventsRepo,
        \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger,
        \Magento\Framework\App\ResourceConnection $resource,
        \Webkul\Helpdesk\Helper\Tickets $ticketsHelper,
        \Webkul\Helpdesk\Model\TicketsFactory $ticketsFactory,
    ) {
        $this->_connectemailFactory = $connectemailFactory;
        $this->_mailfetchFactory = $mailfetchFactory;
        $this->_threadRepo = $threadRepo;
        $this->_ticketsRepo = $ticketsRepo;
        $this->_eventsRepo = $eventsRepo;
        $this->_helpdeskLogger = $helpdeskLogger;
        $this->connection = $resource->getConnection();
        $this->resource = $resource;
        $this->_ticketsHelper = $ticketsHelper;
        $this->_ticketsFactory = $ticketsFactory;
    }

    /**
     * FetchMail Fetch the email and create tickets and threads
     *
     * @param  Int $connectEmailId connect Email Id
     * @return Int Fetch email count
     */
    public function fetchMail($connectEmailId)
    {
        try {
            $connectEmail = $this->_connectemailFactory->create()->load($connectEmailId);
            $host = $connectEmail->getHostName();
            $port = $connectEmail->getPort();
            $userName = $connectEmail->getUsername();
            $password = $this->_ticketsHelper->decryptData($connectEmail->getPassword());
            $server = new \Fetch\Server($host, $port);
            $server->setAuthentication($userName, $password);
            $server->setFlag($connectEmail->getProtocol());
            $server->setMailBox($connectEmail->getMailboxFolder());
            $count = 0;
            if ($connectEmail->getCount() != $connectEmail->getFetchEmailLimit()) {
                $count = $connectEmail->getCount();
            }
            $limit = $count;
             $messages = array_reverse($server->getMessages($limit));
            
            if ($connectEmail->getCount() != $connectEmail->getFetchEmailLimit()) {
                $messages = array_chunk($messages, $connectEmail->getFetchEmailLimit());
                $messages = $messages[0];
            }
            $count = 0;
            
            foreach ($messages as $message) {
                    $flag = $this->processMail($message, $connectEmailId);
                if ($flag) {
                    $count++;
                    if ($connectEmail->getHelpdeskAction() == 1) {
                          $message->delete();
                    } elseif ($connectEmail->getHelpdeskAction() == 2) {
                        if (!$server->hasMailBox($connectEmail->getMailboxFolder())) {
                            $server->createMailBox($connectEmail->getMailboxFolder());
                        }
                        $server->createMailBox($connectEmail->getMailboxFolder());
                        $message->moveToMailBox($connectEmail->getMailboxFolder());
                    }
                }
            }
            $connectEmail->setCount($connectEmail->getCount()+$connectEmail->getFetchEmailLimit());
            $connectEmail->save();
            return $count;
        } catch (\RuntimeException $e) {
            $this->_helpdeskLogger->info($e->getMessage());
            throw new CouldNotSaveException(__($e->getMessage()), $e);
        } catch (\InvalidArgumentException $e) {
            $this->_helpdeskLogger->info($e->getMessage());
            throw new CouldNotSaveException(__($e->getMessage()), $e);
        } catch (\Exception $e) {
            $this->_helpdeskLogger->info($e->getMessage());
            throw new CouldNotSaveException(__($e->getMessage()), $e);
        }
    }

    /**
     * ProcessMail Process the mail the create ticket from them
     *
     * @param  Object $message        Mail message object
     * @param  Int    $connectEmailId connect Email Id
     * @return Int Fetch email count
     */
    public function processMail($message, $connectEmailId)
    {
        $isHtml = true;
        $data = [];
        $header = $message->getHeaders();
        $count = 0;
        
        $mailFetchCollection = $this->_mailfetchFactory->create()->getCollection()
                ->addFieldToFilter("u_id", ["eq"=>$message->getUid()]);
        if (!count($mailFetchCollection)) {
            $toAddress = $message->getAddresses("to");
            $to = [];
            $toAddress = is_array($toAddress) ? $toAddress : [];
            foreach ($toAddress as $value) {
                $to[] = $value['address'];
            }
            $address = $message->getAddresses("sender");
            $data['connect_email_id'] = $connectEmailId;
            $data['source'] = "email";
            $data['who_is'] = "customer";
            $data['email'] = $address['address'];
            $data['from'] = $address['address'];
            $data['to'] = implode(',', $to);
            if (isset($header->ccaddress)) {
                $data['cc'] = $header->ccaddress;
            }
            if (isset($header->bccaddress)) {
                $data['bcc'] = $header->bccaddress;
            }
            $data['fullname'] = isset($address['name'])?$address['name']:"";
            if ($data['fullname'] == "") {
                $data['fullname'] = $data['email'];
            }
            $data['subject'] = $message->getSubject()??"No Subject";
            $data['query'] = $message->getMessageBody($isHtml) ? $message->getMessageBody($isHtml) : "No Message Body";
                
            if ($this->saveReplyForExistingTicket($data, $message) == false) {
                $ticketId = $this->_ticketsRepo->createTicket($data);
                $data['thread_type'] = "create";

                $threadId = $this->_threadRepo->createThread($ticketId, $data);
                $this->_eventsRepo->checkTicketEvent("ticket", $ticketId, "created");
                if (isset($header->message_id)) {
                    $mailFetchData[$header->message_id]['thread_id'] = $threadId;
                    $mailFetchData[$header->message_id]['message_id'] = $header->message_id;
                    $mailFetchData[$header->message_id]['sender'] = $address['address'];
                    $mailFetchData[$header->message_id]['u_id'] = $message->getUid();
                    $this->saveMailMessageData($mailFetchData);
                    $count++;
                }
            }
        }
        return $count;
    }

    /**
     * Save reply on existing ticket
     *
     * @param  object $data
     * @param  object $message
     */
    private function saveReplyForExistingTicket($data, $message)
    {
        $mailFetchCollection = $this->_mailfetchFactory->create()->getCollection()
            ->addFieldToFilter("sender", ["eq"=>$data['email']]);

        $isTicket = stristr($data['subject'], 'Ticket #');
        if (count($mailFetchCollection) && !empty($isTicket)) {
            try {
                // Extracting ticket id from email subject
                $strArray = explode(' ', stristr($data['subject'], '#'));
                $ticketIdStr = $strArray[0];
                $ticketId = substr($ticketIdStr, 1);
                $data['thread_type'] = "reply";
                
                //filter query according to strings received
                $query = explode('<div class="gmail_quote">', $data['query']);
                $data['query'] = $query[0];
                $id = $this->_ticketsFactory->create()->load($ticketId)->getId();
                if (!$id) {
                    return false;
                }
                $this->_eventsRepo->checkTicketEvent("reply", $ticketId, "customer");
                $threadId = $this->_threadRepo->createThread($ticketId, $data);
                if ($threadId) {
                    $this->_ticketsHelper->UpdateTicketTimeStamp($ticketId);
                }
                //saving data in mail detail table
                $header = $message->getHeaders();
                $header->message_id = substr(str_shuffle(sha1(time())), 0, 10);
                $mailFetchData[$header->message_id]['thread_id'] = $threadId;
                $mailFetchData[$header->message_id]['message_id'] = $header->message_id;
                $mailFetchData[$header->message_id]['sender'] = $data['email'];
                $mailFetchData[$header->message_id]['u_id'] = $message->getUid();
                $this->saveMailMessageData($mailFetchData);
                return true;

            } catch (\Exception $e) {
                throw new CouldNotSaveException(__($e->getMessage()), $e);
            }
        }

        return false;
    }

    /**
     * Save mail message data
     *
     * @param  array $bulkInsert
     */
    public function saveMailMessageData($bulkInsert)
    {
        $this->insertMultiple(self::TABLENAME, $bulkInsert);
    }

    /**
     * Save mail data in database
     *
     * @param  object $table
     * @param  array $data
     */
    public function insertMultiple($table, $data)
    {
        try {
            $tableName = $this->resource->getTableName($table);
            return $this->connection->insertMultiple($tableName, $data);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()), $e);
        }
    }
}
