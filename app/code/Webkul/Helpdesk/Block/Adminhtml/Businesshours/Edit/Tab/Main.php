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

class Main extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Config\Model\Config\Source\Locale\Timezone
     */
    protected $_timezone;

    /**
     * @param \Magento\Backend\Block\Template\Context             $context
     * @param \Magento\Framework\Registry                         $registry
     * @param \Magento\Framework\Data\FormFactory                 $formFactory
     * @param \Magento\Config\Model\Config\Source\Locale\Timezone $timezone
     * @param array                                               $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Config\Model\Config\Source\Locale\Timezone $timezone,
        array $data = []
    ) {
        $this->_timezone = $timezone;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form fields
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    public function _prepareForm()
    {
    /**
     * Form variables
     *
     * @var $model \Magento\User\Model\User
     * @var \Magento\Framework\Data\Form $form
    */
        $businesshoursmodel = $this->_coreRegistry->registry('helpdesk_businesshours');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('event_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Add Business Hours'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);

        $fieldset->addField(
            'businesshour_name',
            'text',
            ['name' => 'businesshour_name', 'label' => __('Business Hour Name'),
            'title' => __('Business Hour Name'), 'required' => true]
        );

        $fieldset->addField(
            'description',
            'textarea',
            [
                'name' => 'description',
                'label' => __('Description'),
                'id' => 'description',
                'title' => __('Description'),
                'class' => 'required-entry',
                'required' => true
            ]
        );

        $fieldset->addField(
            'timezone',
            'select',
            [
                'name'      => 'timezone',
                'label'     => __('Timezone'),
                'title'     => __('Timezone'),
                'class'     => 'required-entry',
                'required'  => true,
                'values'    => $this->_getAllTimeZones()
            ]
        );

        $form->setValues($businesshoursmodel->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Get all time zones
     *
     * @return array
     */
    protected function _getAllTimeZones()
    {
        $timezonesArray = [];
        $timezones = $this->_timezone->toOptionArray();
        array_push($timezonesArray, ["value" => "", "label" => "Please Select"]);
        foreach ($timezones as $timezoneData) {
            array_push($timezonesArray, ["value" => $timezoneData['value'], "label" => $timezoneData['label']]);
        }
        return $timezonesArray;
    }
}
