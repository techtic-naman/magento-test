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
namespace Webkul\Helpdesk\Block\Adminhtml\Slapolicy\Edit\Tab;

class Condition extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Prepare form fields
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    public function _prepareForm()
    {
    /**
     * Form variable name
     *
     * @var \Magento\Framework\Data\Form $form
    */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('slapolicy_');

        $baseFieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Conditions for SLA')]
        );

        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare the layout.
     *
     * @return $this
     */
    public function getFormHtml()
    {
        $html = parent::getFormHtml();
        $html .= $this->getLayout()->createBlock(
            \Webkul\Helpdesk\Block\Adminhtml\Slapolicy\Edit\Tab\ConditionTemplate::class
        )->toHtml();
        return $html;
    }
}