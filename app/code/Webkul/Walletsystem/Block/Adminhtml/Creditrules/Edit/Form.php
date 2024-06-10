<?php
/**
 * Webkul Software
 *
 * @category Webkul
 * @package Webkul_Walletsystem
 * @author Webkul
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */

namespace Webkul\Walletsystem\Block\Adminhtml\Creditrules\Edit;

use Webkul\Walletsystem\Model\Walletcreditrules;

/**
 * Webkul Walletsystem Creditrules BLock Form
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Init form
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('edit_form');
        $this->setTitle(__('Wallet system Credit Rule'));
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('wallet_creditrule');
        $form = $this->_formFactory->create(
            ['data' =>
                ['id' => 'edit_form',
                'action' => $this->getData('action'),
                'method' => 'post']
            ]
        );
        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General Information'), 'class' => 'fieldset-wide']
        );
        if ($model && $model->getEntityId()) {
            $fieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
        }
        $fieldset->addField(
            'based_on',
            'select',
            [
                'label' => __('Credit Rule Based'),
                'title' => __('Credit Rule Based'),
                'name' => 'based_on',
                'required' => true,
                'options' => [
                    Walletcreditrules::WALLET_CREDIT_RULE_BASED_ON_PRODUCT => __('On Product'),
                    Walletcreditrules::WALLET_CREDIT_RULE_BASED_ON_CART=> __('On Cart')
                ]
            ]
        );
        $fieldset->addField(
            'amount',
            'text',
            [
                'name' => 'amount',
                'label' => __('Cashback Amount'),
                'required' => true,
                'class' =>  'admin__field-text-class required-entry validate-greater-than-zero',
                'placeholder'=>'Amount'
            ]
        );
        $fieldset->addField(
            'minimum_amount',
            'text',
            [
                'name' => 'minimum_amount',
                'label' => __('Minimum Cart/Product Amount'),
                'required' => true,
                'class' =>  'admin__field-text-class required-entry validate-greater-than-zero',
                'placeholder'=>'Minimum Amount'
            ]
        );
        $fieldset->addField(
            'start_date',
            'date',
            [
                'name' => 'start_date',
                'label' => __('Start From Date'),
                'class' => 'validate-no-html-tags',
                'title' => __('Start From Date'),
                'disabled' => false,
                'singleClick'=> true,
                'required' => true,
                'date_format' => 'yyyy-MM-dd',
                'time'=>false,
                'datePickerOptions'=> ['maxDate'=> 'new Date()']
            ]
        )->setAfterElementHtml("<script type=\"text/javascript\">
        //<![CDATA[
          require([
          'jquery',
          'mage/calendar'
            ], function($){
                   $('#start_date').calendar({           
                   hideIfNoPrevNext: true,
                   minDate: new Date(),
                   showOn: 'button',
                   dateFormat: 'yyyy-MM-dd'
                });
            });         
        //]]>
        </script>");
        $fieldset->addField(
            'end_date',
            'date',
            [
                'name' => 'end_date',
                'label' => __('End Date'),
                'class' => 'validate-no-html-tags',
                'title' => __('End Date'),
                'disabled' => false,
                'singleClick'=> true,
                'required' => true,
                'date_format' => 'yyyy-MM-dd',
                'time'=>false
            ]
        )->setAfterElementHtml("<script type=\"text/javascript\">
        //<![CDATA[
          require([
          'jquery',
          'mage/calendar'
            ], function($){
                   $('#end_date').calendar({           
                   hideIfNoPrevNext: true,
                   minDate: new Date(),
                   showOn: 'button',
                   dateFormat: 'yyyy-MM-dd'
                });
            });         
        //]]>
        </script>");
        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Rule Status'),
                'title' => __('Rule Status'),
                'name' => 'status',
                'required' => true,
                'options' => [
                    Walletcreditrules::WALLET_CREDIT_RULE_STATUS_ENABLE => __('Enabled'),
                    Walletcreditrules::WALLET_CREDIT_RULE_STATUS_DISABLE => __('Disabled')
                ]
            ]
        );
        if ($model) {
            $form->setValues($model->getData());
        }
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
