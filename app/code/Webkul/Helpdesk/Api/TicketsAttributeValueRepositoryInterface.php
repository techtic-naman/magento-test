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
interface TicketsAttributeValueRepositoryInterface
{
    /**
     * SaveTicketAttributeValues
     *
     * @param  int $ticketId
     * @param  mixed $wholedata
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function saveTicketAttributeValues($ticketId, $wholedata);

    /**
     * EditTicketAttributeValues save ticket custom attribute values
     *
     * @param  Array $wholedata Post request data
     * @return [type]            [description]
     */
    public function editTicketAttributeValues($wholedata);
}
