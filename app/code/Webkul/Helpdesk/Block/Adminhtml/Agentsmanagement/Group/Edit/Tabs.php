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
 
namespace Webkul\Helpdesk\Block\Adminhtml\Agentsmanagement\Group\Edit;

/**
 * Tickets page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('page_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Group Information'));
    }

    /**
     * Return befor html form
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'main_section',
            [
                'label' => __('Group'),
                'title' => __('Group'),
                'active' => true
            ]
        );
        return parent::_beforeToHtml();
    }
}
