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
interface MailfetchRepositoryInterface
{
    /**
     * FetchMail Fetch the email and create tickets and threads
     *
     * @param  Int $connectEmailId connect Email Id
     * @return Int Fetch email count
     */
    public function fetchMail($connectEmailId);

    /**
     * ProcessMail Process the mail the create ticket from them
     *
     * @param  Object $message        Mail message object
     * @param  Int    $connectEmailId connect Email Id
     * @return Int Fetch email count
     */
    public function processMail($message, $connectEmailId);
}
