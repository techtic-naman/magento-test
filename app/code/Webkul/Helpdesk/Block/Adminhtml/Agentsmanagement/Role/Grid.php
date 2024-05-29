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

namespace Webkul\Helpdesk\Block\Adminhtml\Agentsmanagement\Role;
 
class Grid extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Return grid
     *
     * @return $this
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Webkul_Helpdesk';
        $this->_controller = 'adminhtml_agentsmanagement_role';
        $this->_headerText = __('Role');
        $this->_addButtonLabel = __('Add New Role');
        parent::_construct();
        $this->buttonList->add(
            'add_role',
            [
                'label' => __('Add/Manage Agent Role'),
                'onclick' => "location.href='" . $this->getUrl('helpdesk/agentsmanagement/newroles') . "'",
                'class' => 'apply'
            ]
        );
    }
}
