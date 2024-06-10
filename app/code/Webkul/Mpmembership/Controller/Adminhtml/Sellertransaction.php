<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Mpmembership
 * @author    Webkul Software Private Limited
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Mpmembership\Controller\Adminhtml;

use Magento\Backend\App\Action;

/**
 * Webkul Mpmembership Admin Sellertransaction Controller
 */
abstract class Sellertransaction extends \Magento\Backend\App\Action
{

    /**
     * Check for is allowed.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_Mpmembership::sellertransaction');
    }
}
