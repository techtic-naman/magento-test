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

namespace Webkul\Helpdesk\Cron;

/**
 * custom cron actions
 */
class Mailfetch
{
    /**
     * @var \Webkul\Helpdesk\Logger\HelpdeskLogger
     */
    protected $helpdeskLogger;

    /**
     * @var \Webkul\Helpdesk\Model\MailfetchRepository
     */
    protected $mailfetchRepo;

    /**
     * @var \Webkul\Helpdesk\Model\ConnectEmailFactory
     */
    protected $_connectEmailFactory;

    /**
     * Mailfetch constructor.
     *
     * @param \Webkul\Helpdesk\Logger\HelpdeskLogger     $helpdeskLogger
     * @param \Webkul\Helpdesk\Model\MailfetchRepository $mailfetchRepo
     * @param \Webkul\Helpdesk\Model\ConnectEmailFactory $connectEmailFactory
     */
    public function __construct(
        \Webkul\Helpdesk\Logger\HelpdeskLogger $helpdeskLogger,
        \Webkul\Helpdesk\Model\MailfetchRepository $mailfetchRepo,
        \Webkul\Helpdesk\Model\ConnectEmailFactory $connectEmailFactory
    ) {
        $this->helpdeskLogger = $helpdeskLogger;
        $this->mailfetchRepo = $mailfetchRepo;
        $this->_connectEmailFactory = $connectEmailFactory;
    }

    /**
     * Fetch mail
     */
    public function execute()
    {
        try {
            $this->helpdeskLogger->info("Run Mailfetch Cron Method");
            $connectEmailCollection = $this->_connectEmailFactory->create()
                ->getCollection()
                ->addFieldToFilter("status", ["eq"=>1]);
            foreach ($connectEmailCollection as $connectEmail) {
                $this->mailfetchRepo->fetchMail($connectEmail->getId());
            }
        } catch (\Exception $e) {
            $this->helpdeskLogger->info("cron job error :".$e->getMessage());
        }
    }
}
