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

class Main extends \Magento\Backend\Block\Widget\Form\Generic
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
     * @var $model \Magento\User\Model\User
     * @var \Magento\Framework\Data\Form $form
    */
        $slamodel = $this->_coreRegistry->registry('helpdesk_slapolicy');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('slapolicy_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Add SLA Policy'), 'class' => 'fieldset-wide']
        );

        if ($slamodel->getSlaId()) {
            $fieldset->addField('sla_id', 'hidden', ['name' => 'sla_id']);
        }

        $fieldset->addField(
            'sla_name',
            'text',
            ['name' => 'sla_name', 'label' => __('Name'), 'title' => __('Name'), 'required' => true]
        );

        $fieldset->addField(
            'sla_description',
            'textarea',
            [
                'name' => 'sla_description',
                'label' => __('Description'),
                'id' => 'description',
                'title' => __('Description'),
                'class' => 'required-entry',
                'required' => true
            ]
        );

        $fieldset->addField(
            'sort_order',
            'text',
            [
                'name' => 'sort_order',
                'label' => __('Sort Order'),
                'title' => __('Sort Order'),
                'required' => true,
                'class' => 'required-entry validate-number',
            ]
        );

        $form->setValues($slamodel->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
