<?php

namespace Meetanshi\Bulksms\Block\Adminhtml\Campaign\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Store\Model\System\Store;

class Events extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $_systemStore;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Store $systemStore,
        array $data = []
    ) {

        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('campaign_manage');

        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Details')]);

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Campaign Name'),
                'title' => __('Campaign Name'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'message',
            'textarea',
            [
                'name' => 'message',
                'label' => __('SMS Marketing Text'),
                'title' => __('SMS Marketing Text'),
                'required' => true,
                'note' => 'You can make use of {{shop_name}},{{shop_url}},{{username}} variables in your SMS text.'
            ]
        );

        $fieldset->addField(
            'startdate',
            'date',
            [
                'name' => 'startdate',
                'label' => __('Campaign Schedule'),
                'title' => __('Campaign Schedule'),
                'required' => true,
                'date_format' => $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT),
                'note'=>'Select the date of your campaign to be run.'
            ]
        );


        $hourArr = [];
        for ($j = 1; $j <= 24; $j++) {
            $hourArr[$j] = $j;
        }

        $fieldset->addField(
            'hour',
            'select',
            [
                'name' => 'hour',
                'label' => __('Select Hours'),
                'title' => __('Select Hours'),
                'required' => true,
                'values' => $hourArr,
                'note'=>'Select the time of your campaign to be run.'
            ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'title' => __('Status'),
                'required' => true,
                'values' => [
                    '1' => 'Enable ',
                    '0' => 'Disable',
                ]
            ]
        );




        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }


    public function getTabLabel()
    {
        return __('Details');
    }

    public function getTabTitle()
    {
        return __('Details');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    protected function _isAllowedAction($resourceId)
    {
        return true;
    }
}
