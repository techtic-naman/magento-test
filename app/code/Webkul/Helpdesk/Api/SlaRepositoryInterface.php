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
interface SlaRepositoryInterface
{
    /**
     * CheckTicketConditionForSla Record the ticket SLA
     *
     * @param Int    $ticketId Ticket Id
     * @param String $type     Reply Type
     */
    public function checkTicketConditionForSla($ticketId, $type);

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
