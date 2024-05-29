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
interface ActivityRepositoryInterface
{
    /**
     * SaveActivity Save Helpdesk activity
     *
     * @param  int    $id    Entity Id
     * @param  String $name  Entity Name
     * @param  String $type  Activity Type
     * @param  int    $field Activity Field
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function saveActivity($id, $name, $type, $field);
}
