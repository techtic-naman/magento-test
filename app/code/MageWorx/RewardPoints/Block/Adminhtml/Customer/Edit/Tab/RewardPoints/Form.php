<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Block\Adminhtml\Customer\Edit\Tab\RewardPoints;

use Magento\Customer\Controller\RegistryConstants;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Store\Model\System\StoreFactory
     */
    protected $storeFactory;

    /**
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * @var \Magento\Config\Model\Config\Source\Yesno $yesnoOptions
     */
    protected $yesnoOptions;

    /**
     * @var \MageWorx\RewardPoints\Helper\Data
     */
    protected $helperData;

    /**
     * @var string
     */
    protected $targetForm = 'customer_form';

    /**
     * Form constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\StoreFactory $storeFactory
     * @param \Magento\Customer\Model\CustomerRegistry $customerRegistry
     * @param \Magento\Config\Model\Config\Source\Yesno $yesnoOptions
     * @param \MageWorx\RewardPoints\Helper\Data $helperData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\StoreFactory $storeFactory,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Config\Model\Config\Source\Yesno $yesnoOptions,
        \MageWorx\RewardPoints\Helper\Data $helperData,
        array $data = []
    ) {
        $this->storeFactory     = $storeFactory;
        $this->customerRegistry = $customerRegistry;
        $this->yesnoOptions     = $yesnoOptions;
        $this->helperData       = $helperData;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        $customerId = $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);

        return $this->customerRegistry->retrieve($customerId);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $targetForm = $this->getData('target_form') ? $this->getData('target_form') : $this->targetForm;

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('mageworx_rewardpoints_');
        $form->setFieldNameSuffix('mageworx_rewardpoints_data');

        $fieldset = $form->addFieldset(
            'rewardpoints_update_fieldset',
            ['legend' => __('Update Reward Points Balance')]
        );

        if (!$this->_storeManager->isSingleStoreMode()) {
            $fieldset->addField(
                'store',
                'select',
                [
                    'name'           => 'store_id',
                    'title'          => __('Store'),
                    'label'          => __('Store'),
                    'values'         => $this->getStoreValues(),
                    'data-form-part' => $targetForm
                ]
            );
        }

        $fieldset->addField(
            'points_delta',
            'text',
            [
                'name'           => 'points_delta',
                'title'          => __('Update Points'),
                'label'          => __('Update Points'),
                'note'           => __('It is possible to use a negative variable when subtracting points.'),
                'data-form-part' => $targetForm
            ]
        );

        if ($this->helperData->isEnableExpirationDate()) {
            $fieldset->addField(
                'expiration_period',
                'text',
                [
                    'name'           => 'expiration_period',
                    'title'          => __('New Expiration Period'),
                    'label'          => __('New Expiration Period'),
                    'value'          => 0,
                    'note'           => $this->getExpirationPeriodNote(),
                    'class'          => 'validate-not-negative-number',
                    'data-form-part' => $targetForm
                ]
            );
        }

        $fieldset->addField(
            'is_need_send_notification',
            'select',
            [
                'name'           => 'is_need_send_notification',
                'title'          => __('Send Notification'),
                'label'          => __('Send Notification'),
                'values'         => $this->yesnoOptions->toOptionArray(),
                'note'           => $this->getSendNotificationNote(),
                'data-form-part' => $targetForm
            ]
        );

        $fieldset->addField(
            'comment',
            'text',
            [
                'name'           => 'comment',
                'title'          => __('Comment for Customer'),
                'label'          => __('Comment for Customer'),
                'data-form-part' => $targetForm
            ]
        );

        $this->restoreFromSession($form, $this->getCustomer()->getId());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     *
     * @param \Magento\Framework\Data\Form $form
     * @param int $customerId
     * @return void
     */
    protected function restoreFromSession(\Magento\Framework\Data\Form $form, $customerId)
    {
        $formData = $this->_backendSession->getCustomerFormData();
        if (empty($formData)) {
            return;
        }

        $formDataCustomerId = $formData['customer']['entity_id'] ?? null;
        $rewardpointsData   = $formData['mageworx_rewardpoints_data'] ?? null;

        if ($formDataCustomerId != $customerId || !$rewardpointsData) {
            return;
        }

        if (isset($rewardpointsData['is_need_send_notification'])) {
            $form->getElement('is_need_send_notification')
                 ->setIsChecked($rewardpointsData['is_need_send_notification']);
            unset($rewardpointsData['is_need_send_notification']);
        }
        $form->addValues($rewardpointsData);
    }

    /**
     * @return array
     */
    protected function getStoreValues()
    {
        $customer = $this->getCustomer();
        if (!$customer->getWebsiteId()
            || $this->_storeManager->hasSingleStore()
            || $customer->getSharingConfig()->isGlobalScope()
        ) {
            return $this->storeFactory->create()->getStoreValuesForForm();
        }

        return $this->getCustomerWebsiteStoreValues($customer);


    }

    /**
     * @param int $websiteId
     * @return array
     */
    protected function getCustomerWebsiteStoreValues($customer)
    {
        $websites = $this->storeFactory->create()->getStoresStructure(
            false,
            [],
            [],
            [$customer->getWebsiteId()]
        );
        $values   = [];

        $nonEscapableNbspChar = html_entity_decode('&#160;', ENT_NOQUOTES, 'UTF-8');

        foreach ($websites as $websiteId => $website) {
            $values[] = ['label' => $website['label'], 'value' => []];

            if (empty($website['children'])) {
                continue;
            }

            foreach ($website['children'] as $groupId => $group) {

                if (empty($group['children'])) {
                    continue;
                }

                $options = [];
                foreach ($group['children'] as $storeId => $store) {
                    $options[] = [
                        'label' => str_repeat($nonEscapableNbspChar, 4) . $store['label'],
                        'value' => $store['value'],
                    ];
                }
                $values[] = [
                    'label' => str_repeat($nonEscapableNbspChar, 4) . $group['label'],
                    'value' => $options,
                ];
            }

        }

        return $values;
    }

    /**
     * @return int
     */
    protected function getExpirationPeriod()
    {
        $websiteId = $this->getCustomer()->getWebsiteId();

        return $this->helperData->getDefaultExpirationPeriod($websiteId);
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected function getExpirationPeriodNote()
    {
        return __('0 - remains unchanged.') . ' '
            . __('Leave empty for the set unlimited expiration date.') . '<br>'
            . __('For reference:') . ' '
            . __('The "Default Expiration Period" is %1 days.', $this->getExpirationPeriod());
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    protected function getSendNotificationNote()
    {
        return __(
            "Notifications are  sent only in case of the points balance changes."
        );
    }

    /**
     * @return bool
     */
    protected function isEnableExpirationDate()
    {
        $websiteId = $this->getCustomer()->getWebsiteId();

        return $this->helperData->isEnableExpirationDate($websiteId);
    }
}
