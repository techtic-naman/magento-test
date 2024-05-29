<?php

namespace Meetanshi\Bulksms\Block\Adminhtml\Campaign;

use Magento\Backend\Block\Widget\Form\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

class Edit extends Container
{
    protected $registry = null;

    public function __construct(Context $context, Registry $registry, array $data = [])
    {
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    public function getHeaderText()
    {
        $title = __("Manage Campaign");
        return $title;
    }

    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_campaign';
        $this->_blockGroup = 'Meetanshi_Bulksms';
        $this->_headerText = __('Manage Campaign');
        parent::_construct();


        $this->buttonList->update('save', 'label', __('Save Capmaign'));
        $this->buttonList->remove('reset');

        if ($this->getRequest()->getParam('id')) {
            $this->addButton(
                'delete',
                [
                    'label' => __('Delete Option'),
                    'onclick' => 'deleteConfirm(' . json_encode(__('Are you sure you want to do this?'))
                        . ','
                        . json_encode($this->getDeleteUrl())
                        . ')',
                    'class' => 'scalable delete',
                    'level' => -1
                ]
            );
        }
        $this->buttonList->add(
            'saveandcontinue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']]
                ]
            ],
            -100
        );
    }

    public function getDeleteUrl(array $args = [])
    {
        return $this->getUrl('*/*/delete', ['_current' => true, 'id' => $this->getRequest()->getParam('id')]);
    }
}
