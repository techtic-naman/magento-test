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
namespace Webkul\Helpdesk\Block\Adminhtml\Businesshours\Edit\Tab;

class Holidays extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Prepare form fields
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    public function _prepareForm()
    {
    /**
     * @var \Magento\Framework\Data\Form $form
    */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('businesshours_');

        $baseFieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Yearly Holiday List')]
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
            \Webkul\Helpdesk\Block\Adminhtml\Businesshours\Edit\Tab\HolidaysTemplate::class
        )->toHtml();
        return $html;
    }
}