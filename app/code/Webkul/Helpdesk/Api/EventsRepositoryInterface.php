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

namespace Webkul\Helpdesk\Api;

/**
 * @api
 * @since 100.0.2
 */
interface EventsRepositoryInterface
{
    /**
     * CheckTicketEvent Record the ticket event
     *
     * @param String $actionType Event Type
     * @param Int    $ticketId   Ticket Id
     * @param Sting  $from       From
     * @param Sting  $to         to
     */
    public function checkTicketEvent($actionType, $ticketId, $from, $to);

    /**
     * CheckTicketConditionForSla Check ticket condition for ticket rules
     *
     * @param int $ticketId    Ticket Id
     * @param int $eventId     Event Id
     */
    public function checkTicketCondition($ticketId, $eventId);

    /**
     * CheckCondition Rules for condition check
     *
     * @param  String $condition Condition
     * @param  String $haystack  Haystack
     * @param  String $needle    Needle
     * @return Booleam
     */
    public function checkCondition($condition, $haystack, $needle);
}
