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
interface AttachmentRepositoryInterface
{
    /**
     * SaveActivity Save Helpdesk activity
     *
     * @param  int    $ticket_id Entity Id
     * @param  String $thread_id Entity Name
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function saveAttachment($ticket_id, $thread_id);
}
