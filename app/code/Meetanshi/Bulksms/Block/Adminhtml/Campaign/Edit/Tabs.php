<?php

namespace Meetanshi\Bulksms\Block\Adminhtml\Campaign\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Tabs as WidgetTabs;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Registry;

class Tabs extends WidgetTabs
{
    protected $registry;

    public function __construct(Registry $registry, Context $context, EncoderInterface $jsonEncoder, Session $authSession)
    {
        $this->registry = $registry;
        parent::__construct($context, $jsonEncoder, $authSession);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('campaign_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Campaign Information'));
    }

    protected function _beforeToHtml()
    {

        $this->addTab('details', [
            'label' => __('Campaign Details'),
            'title' => __('Campaign Details'),
            'content' => $this->getLayout()->createBlock('\Meetanshi\Bulksms\Block\Adminhtml\Campaign\Edit\Tab\Events')->toHtml(),
        ]);

        $this->addTab('phonebook', [
            'label' => __('Add Contacts'),
            'title' => __('Add Contacts'),
            'content' => $this->getLayout()->createBlock('\Meetanshi\Bulksms\Block\Adminhtml\Campaign\Edit\Tab\Phonebook')->toHtml(),
        ]);

        $this->_updateActiveTab();

        return parent::_beforeToHtml();
    }

    protected function _updateActiveTab()
    {
        $tabId = $this->getRequest()->getParam('tab');
        if ($tabId) {
            $tabId = preg_replace("#{$this->getId()}_#", '', $tabId);
            if ($tabId) {
                $this->setActiveTab($tabId);
            }
        } else {
            $this->setActiveTab('main');
        }
    }
}
