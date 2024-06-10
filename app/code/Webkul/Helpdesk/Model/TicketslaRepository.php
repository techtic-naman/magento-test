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

class TicketslaRepository implements \Webkul\Helpdesk\Api\TicketslaRepositoryInterface
{
    /**
     * @var \Magento\Framework\Session\SessionManager
     */
    protected $_sessionManager;

    /**
     * @var \Webkul\Helpdesk\Model\EventsFactory
     */
    protected $_eventsFactory;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonHelper;

    /**
     * @var \Webkul\Helpdesk\Logger\HelpdeskLogger
     */
    protected $_helpdeskLogger;

    /**
     * @var \Webkul\Helpdesk\Model\TicketsFactory
     */
    protected $_ticketsFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var \Webkul\Helpdesk\Model\CustomerOrganizationFactory
     */
    protected $_custOrgFactory;

    /**
     * @var \Webkul\Helpdesk\Model\ResponsesRepository
     */
    protected $_responseRepo;
    protected $_slapolicyFactory;
    protected $_groupFactory;
    protected $_businesshoursFactory;
    protected $_ticketslaFactory;
    protected $_threadFactory;
    protected $serializer;

    /**
     * TicketsRepository constructor.
     *
     * @param \Magento\Framework\Session\SessionManager          $sessionManager
     * @param \Webkul\Helpdesk\Model\SlapolicyFactory            $slapolicyFactory
     * @param \Magento\Framework\Json\Helper\Data                $jsonHelper
     * @param \Webkul\Helpdesk\Logger\HelpdeskLogger             $helpdeskLogger
     * @param \Webkul\Helpdesk\Model\TicketsFactory              $ticketsFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime        $date
     * @param \Webkul\Helpdesk\Model\CustomerOrganizationFactory $custOrgFactory
     * @param \Webkul\Helpdesk\Model\ResponsesRepository         $responseRepo
     * @param \Webkul\Helpdesk\Model\GroupFactory                $groupFactory
     * @param \Webkul\Helpdesk\Model\BusinesshoursFactory        $businesshoursFactory
     * @param \Webkul\Helpdesk\Model\TicketslaFactory            $ticketslaFactory
     * @param \Webkul\Helpdesk\Model\ThreadFactory               $threadFactory
     * @param \Magento\Framework\Serialize\SerializerInterface   $serializer
     */
    public function __construct(
        \Magento\Framework\Session\SessionManager $sessionManager,
        \Webkul\Helpdesk\Model\SlapolicyFactory $slapolicyFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger,
        \Webkul\Helpdesk\Model\TicketsFactory $ticketsFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Webkul\Helpdesk\Model\CustomerOrganizationFactory $custOrgFactory,
        \Webkul\Helpdesk\Model\ResponsesRepository $responseRepo,
        \Webkul\Helpdesk\Model\GroupFactory $groupFactory,
        \Webkul\Helpdesk\Model\BusinesshoursFactory $businesshoursFactory,
        \Webkul\Helpdesk\Model\TicketslaFactory $ticketslaFactory,
        \Webkul\Helpdesk\Model\ThreadFactory $threadFactory,
        \Magento\Framework\Serialize\SerializerInterface $serializer
    ) {
        $this->_sessionManager = $sessionManager;
        $this->_slapolicyFactory = $slapolicyFactory;
        $this->_jsonHelper = $jsonHelper;
        $this->_helpdeskLogger = $helpdeskLogger;
        $this->_ticketsFactory = $ticketsFactory;
        $this->_date = $date;
        $this->_custOrgFactory = $custOrgFactory;
        $this->_responseRepo = $responseRepo;
        $this->_groupFactory = $groupFactory;
        $this->_businesshoursFactory = $businesshoursFactory;
        $this->_ticketslaFactory = $ticketslaFactory;
        $this->_threadFactory = $threadFactory;
        $this->serializer = $serializer;
    }

    /**
     * ApplyReplySLAToTicket Apply Sla on ticket during reply added
     *
     * @param Int $ticketId Ticket Id
     * @param Int $slaId    Sla Id
     */
    public function applyReplySLAToTicket($ticketId, $slaId)
    {
        try {
            $ticket = $this->_ticketsFactory->create()->load($ticketId);
            $slaPolicy = $this->_slapolicyFactory->create()->load($slaId);
            $ticketGroup = $this->_groupFactory->create()->load($ticket->getToGroup());
            $businessHours = $this->_businesshoursFactory->create()->load($ticketGroup->getBusinesshourId());
            $ticketSla = $this->_ticketslaFactory->create()->getCollection()
                ->addFieldToFilter("ticket_id", $ticketId)
                ->getFirstItem();
            if ($ticketSla->getSize()) {
                $slaTargets = $this->serializer->unserialize($slaPolicy->getSlaServiceLevelTargets());
                $slaTargets = $slaTargets['targets'][$ticket->getPriority()];
                $ticketSlaModel = $this->_ticketslaFactory->create()->load($ticketSla->getId());
                if ($slaTargets['operational-hours'] == '2' || $ticket->getToGroup() == 0) {
                    $responedTime = date(
                        'Y-m-d H:i:s',
                        strtotime(
                            $this->_date->gmtDate('Y-m-d H:i:s') . "+".$slaTargets
                            ['response-in']." ".$slaTargets['response-time-type']
                        )
                    );
                    $ticketSlaModel->setTicketId($ticketId);
                    $ticketSlaModel->setRespondTime($responedTime);
                    $ticketSlaModel->save();
                } else {
                    $offset = $this->_businesshoursFactory->create()->getBusinesshourOffset($businessHours->getId());
                    $today = date('Y-m-d H:i:s', strtotime($this->_date->gmtDate('Y-m-d H:i:s')) + $offset);
                    $data = [
                        'is_working_day' => 1,
                        "day"=>$today,
                        "business_hours"=>$businessHours,
                        "sla_targets"=>$slaTargets,
                        "type"=>'response'
                    ];
                    $responedTime = $this->calculateTime($data);
                    $ticketSlaModel->setTicketId($ticketId);
                    $ticketSlaModel->setRespondTime($responedTime);
                    $ticketSlaModel->save();
                }
            }
        } catch (\Exception $e) {
            $this->_helpdeskLogger->info($e->getMessage());
            throw new CouldNotSaveException(__('There are some error in ticketsla'), $e);
        }
    }

    /**
     * ApplySLAToTicket Apply Sla on ticket during ticket creation
     *
     * @param Int $ticketId Ticket Id
     * @param Int $slaId    Sla Id
     */
    public function applySLAToTicket($ticketId, $slaId)
    {
        $ticket = $this->_ticketsFactory->create()->load($ticketId);
        $slaPolicy = $this->_slapolicyFactory->create()->load($slaId);
        $ticketGroup = $this->_groupFactory->create()->load($ticket->getToGroup());
        $businessHours = $this->_businesshoursFactory->create()->load($ticketGroup->getBusinesshourId());
        $ticketSla = $this->_ticketslaFactory->create();
        $slaTargets = $this->serializer->unserialize(($slaPolicy->getSlaServiceLevelTargets()));
        $slaTarget = $slaTargets['targets'][$ticket->getPriority()];
        if ($slaTarget['operational-hours'] == '2' || $ticket->getToGroup() == 0) {
            $responedTime = date(
                'Y-m-d H:i:s',
                strtotime(
                    $this->_date->gmtDate('Y-m-d H:i:s') . "+".$slaTarget['response-in']." 
                ".$slaTarget['response-time-type']
                )
            );
            $resolveTime = date(
                'Y-m-d H:i:s',
                strtotime(
                    $this->_date->gmtDate('Y-m-d H:i:s') . "+".$slaTarget['resolve-in']
                    ." ".$slaTarget['resolve-time-type']
                )
            );
            $ticketSla->setTicketId($ticketId);
            $ticketSla->setRespondTime($responedTime);
            $ticketSla->setResolveTime($resolveTime);
            $ticketSla->save();
        } else {
            $offset = $this->_businesshoursFactory->create()->getBusinesshourOffset($businessHours->getId());
            $today = date('Y-m-d H:i:s', strtotime($this->_date->gmtDate('Y-m-d H:i:s')) + $offset);
            $data = ['is_working_day' => 1, "day"=>$today, "business_hours"=>$businessHours,
            "sla_targets"=>$slaTarget, "type"=>'response'];
            $responedTime = $this->calculateTime($data);
            $resolveTime = $this->calculateTime($data);
            $ticketSla->setTicketId($ticketId);
            $ticketSla->setRespondTime($responedTime);
            $ticketSla->setResolveTime($resolveTime);
            $ticketSla->save();
        }
    }

    /**
     * CalculateTime Remaining time for resolve and respond ticket
     *
     * @param  array $data SLA details
     * @return Datatime $returnTime Resolve and Respond ticket
     */
    public function calculateTime($data)
    {
        try {
            $returnTime = "";
            $businessHours = $data['business_hours'];
            $flag = 0;
            if (count($businessHours->getData())) {
                $offset = $this->_businesshoursFactory->create()->getBusinesshourOffset($businessHours->getId());
                $type = $data['type'];
                $slaTargets = $data['sla_targets'];
                $helpdesk_hours = $this->serializer->unserialize($businessHours->getHelpdeskHours());
                $day = date("l", strtotime($data['day']));
                $datetime = new \DateTime($data['day']);
                if (isset($helpdesk_hours[$day])) {
                    $holidays = [];
                    $hollydayList = $this->serializer->unserialize($businessHours->getHollydayList());
                    foreach ($hollydayList as $key => $date) {
                        array_push(
                            $holidays,
                            str_pad($date['month'], 2, '0', STR_PAD_LEFT)
                            ."-".str_pad($date['day'], 2, '0', STR_PAD_LEFT)
                        );
                    }
                    if (in_array($datetime->format('m-d'), $holidays)) {
                        $flag = 1;
                    }
                    list($flag, $returnTime, $data) = $this->checkFlag($flag, $data, $helpdesk_hours, $day, $type, $slaTargets);
                } else {
                    $flag = 1;
                }
            }
            if ($flag) {
                $datetime->modify('+1 day');
                $data['day'] = $datetime->format('Y-m-d H:i:s');
                $data['is_working_day'] = 0;
                $returnTime = $this->calculateTime($data);
            }
            return $returnTime;
        } catch (\Exception $e) {
            $this->_helpdeskLogger->info($e->getMessage());
            throw new CouldNotSaveException(__('There are some error in ticketsla'), $e);
        }
    }

        /**
     * Check flag to calcuate remaining time for resolve and respond ticket
     *
     * @param boolean $flag
     * @param array   $data
     * @param string  $helpdesk_hours
     * @param string  $day
     * @param string  $type
     * @param array   $slaTargets
     */
    public function checkFlag($flag, $data, $helpdesk_hours, $day, $type, $slaTargets)
    {
        $returnTime = "";
        if (!$flag) {
            $currentTime = $data['day'];
            $helpdesk_hours = $helpdesk_hours[$day];
            $startTime = date('Y-m-d', strtotime($data['day']))." ".$helpdesk_hours
            ['morning_'.$day]." ".$helpdesk_hours['morning_cycle_'.$day];
            $startTime = date('Y-m-d H:i:s', strtotime($startTime));
            $endTime = date('Y-m-d', strtotime($data['day']))." ".$helpdesk_hours
            ['evening_'.$day]." ".$helpdesk_hours['evening_cycle_'.$day];
            $endTime = date('Y-m-d H:i:s', strtotime($endTime));
            if (isset($data['remaining_time']) && $data['remaining_time'] > 0) {
                $responedTime = date(
                    'Y-m-d H:i:s',
                    strtotime($startTime) +
                    $data['remaining_time']
                );
            } elseif (!$data['is_working_day']) {
                $responedTime = date(
                    'Y-m-d H:i:s',
                    strtotime(
                        $startTime . "+".$slaTargets[$type.'-in']." ".$slaTargets
                        [$type.'-time-type']
                    )
                );
            } else {
                $responedTime = date(
                    'Y-m-d H:i:s',
                    strtotime(
                        $data['day'] . "
                +".$slaTargets[$type.'-in']." ".$slaTargets[$type.'-time-type']
                    )
                );
            }

            if (isset($data['remaining_time']) && $data['remaining_time'] <= 0) {
                $returnTime = $responedTime;
            } elseif ((isset($data['remaining_time']) && $data['remaining_time'] > 0)
                || !$data['is_working_day']
            ) {
                if (strtotime($endTime) - strtotime($responedTime) < 0) {
                    $remainingTime = abs(strtotime($endTime) - strtotime($responedTime));
                    $data['remaining_time'] = $remainingTime;
                    $flag = 1;
                } else {
                    $returnTime = $responedTime;
                }
            } else {
                if (strtotime($startTime) > strtotime($currentTime)) {
                    $responedTime = date(
                        'Y-m-d H:i:s',
                        strtotime(
                            $startTime . "
                    +".$slaTargets[$type.'-in']." ".$slaTargets[$type.'-time-type']
                        )
                    );
                    if (strtotime($endTime) - strtotime($responedTime) < 0) {
                        $remainingTime = abs(strtotime($endTime) - strtotime($responedTime));
                        $data['remaining_time'] = $remainingTime;
                        $flag = 1;
                    } else {
                        $returnTime = $responedTime;
                    }
                } elseif (strtotime($startTime) < strtotime($currentTime)
                    && strtotime($endTime) > strtotime($currentTime)
                ) {
                    if (strtotime($endTime) - strtotime($responedTime) < 0) {
                        $remainingTime = abs(
                            strtotime($endTime) -
                            strtotime($responedTime)
                        );
                        $data['remaining_time'] = $remainingTime;
                        $flag = 1;
                    } else {
                        $returnTime = $responedTime;
                    }
                } else {
                    $day = date("l", strtotime($data['day']."+1 days"));
                    if (isset($helpdesk_hours[$day])) {
                        $startTime = date('Y-m-d', strtotime($data['day']))." 
                        ".$helpdesk_hours['morning_'.$day]." ".$helpdesk_hours
                        ['morning_cycle_'.$day];
                        $startTime = date('Y-m-d H:i:s', strtotime($startTime));
                        $endTime = date('Y-m-d', strtotime($data['day']))." 
                        ".$helpdesk_hours['evening_'.$day]." ".$helpdesk_hours
                        ['evening_cycle_'.$day];
                        $endTime = date('Y-m-d H:i:s', strtotime($endTime));
                        $responedTime = date(
                            'Y-m-d H:i:s',
                            strtotime(
                                $startTime . "+".$slaTargets[$type.'-in']." ".$slaTargets
                                [$type.'-time-type']
                            )
                        );
                        $remainingTime = abs(strtotime($endTime) - strtotime($responedTime));
                        $data['remaining_time'] = $remainingTime;
                        $flag = 1;
                    }
                }
            }
        }
        return [$flag, $returnTime, $data];
    }

    /**
     * GetTicketLastReplyDatails Return Last ticket reply
     *
     * @param int $ticketId Ticket Id
     */
    public function getTicketLastReplyDatails($ticketId)
    {
        return $this->_threadFactory->create()->getCollection()
            ->addFieldToFilter("ticket_id", ["eq"=>$ticketId])
            ->addFieldToFilter(
                ['thread_type', 'thread_type'],
                [
                                                        ['eq'=>'create'],
                                                        ['eq'=>'reply']
                                                    ]
            )
            ->getLastItem();
    }

    /**
     * GetTicketResponseTime Return ticket response time
     *
     * @param  int $ticketId Ticket Id
     * @return string Response time
     */
    public function getTicketResponseTime($ticketId)
    {
        $offset = $this->getTicketOffsetById($ticketId);

        $lastThread = $this->getTicketLastReplyDatails($ticketId);
        if ($lastThread->getWhoIs() == "customer") {
            $flag = 1;
        } else {
            $flag = 0;
        }
        if ($flag==0) {
            return ["flag"=>$flag];
        }

        $ticketSla = $this->_ticketslaFactory->create()->getCollection()
            ->addFieldToFilter("ticket_id", ["eq"=>$ticketId])
            ->getFirstItem();
        if ($ticketSla->getId()) {
            $currentDate = date_create(date('Y-m-d H:i:s', strtotime($this->_date->gmtDate('Y-m-d H:i:s')) + $offset));
            $respondDate = date_create($ticketSla->getRespondTime());
            $diff = date_diff($respondDate, $currentDate);
            if ($diff->invert) {
                $sign = "+";
            } else {
                $sign = "-";
            }
            $days = $diff->d;
            $hours = $diff->h;
            $minutes = $diff->i;
            $seconds = $diff->s;
            $txt = $sign." ".$days." day(s) ".$hours." hour(s) ".$minutes." minute(s) ".$seconds." second(s)";
            return ["flag"=>$flag, "error"=>$diff->invert, "msg"=>$txt];
        }
    }

    /**
     * GetTicketResolveTime Return ticket Resolve time
     *
     * @param  int $ticketId Ticket Id
     * @return string Resolve time
     */
    public function getTicketResolveTime($ticketId)
    {
        $offset = $this->getTicketOffsetById($ticketId);
        $ticketSla = $this->_ticketslaFactory->create()->getCollection()
            ->addFieldToFilter("ticket_id", ["eq"=>$ticketId])
            ->getFirstItem();
        $flag = 0;
        if ($ticketSla->getId() && strtotime($ticketSla->getResolveTime()) > 0) {
            $currentDate = date_create(date('Y-m-d H:i:s', strtotime($this->_date->gmtDate('Y-m-d H:i:s')) + $offset));
            $resolveDate = date_create($ticketSla->getResolveTime());
            $diff = date_diff($resolveDate, $currentDate);
            if ($diff->invert) {
                $sign = "+";
            } else {
                $sign = "-";
            }
            $days = $diff->d;
            $hours = $diff->h;
            $minutes = $diff->i;
            $seconds = $diff->s;
            $txt = $sign." ".$days." day(s) ".$hours." hour(s) ".$minutes." minute(s) ".$seconds." second(s)";
            return ["flag"=>1,"error"=>$diff->invert,"msg"=>$txt];
        }
        return ["flag"=>$flag];
    }

    /**
     * Get business hours timesatmp offset
     *
     * @param  int $ticketId Ticket Id
     */
    public function getTicketOffsetById($ticketId)
    {
        $offset = 0;
        $ticket = $this->_ticketsFactory->create()->load($ticketId);
        $ticketGroup = $this->_groupFactory->create()->load($ticket->getToGroup());
        $businessHours = $this->_businesshoursFactory->create()->load($ticketGroup->getBusinesshourId());
        $offset = $this->_businesshoursFactory->create()->getBusinesshourOffset($businessHours->getId());
        return $offset;
    }
}
