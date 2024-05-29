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
namespace Webkul\Helpdesk\Api\Data;

/**
 * Helpdesk Attachment interface.
 *
 * @api
 */
interface AttachmentInterface
{
    /**
     * #@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    public const ENTITY_ID    = 'id';
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
     * @return \Webkul\Helpdesk\Api\Data\AttachmentInterface
     */
    public function setId($id);
}