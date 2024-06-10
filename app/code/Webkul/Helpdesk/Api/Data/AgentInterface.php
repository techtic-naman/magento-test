<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Helpdesk
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Helpdesk\Api\Data;

/**
 * Marketplace Agent interface.
 *
 * @api
 */
interface AgentInterface
{
    /**
     * #@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    public const ENTITY_ID    = 'entity_id';
    /**
     * #@-
     */

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID
     *
     * @param  int $id
     * @return \Webkul\Helpdesk\Api\Data\TicketsInterface
     */
    public function setId($id);
}
