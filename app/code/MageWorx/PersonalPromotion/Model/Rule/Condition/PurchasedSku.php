<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\PersonalPromotion\Model\Rule\Condition;

use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;
use MageWorx\PersonalPromotion\Model\PurchasesDataProvider;
use Magento\Backend\Helper\Data as BackendData;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;

class PurchasedSku extends AbstractCondition
{
    /**
     * @var PurchasesDataProvider
     */
    protected $purchasesDataProvider;

    /**
     * @var BackendData
     */
    protected $backendData;

    /**
     * PurchasedSku constructor.
     *
     * @param Context $context
     * @param PurchasesDataProvider $purchasesDataProvider
     * @param BackendData $backendData
     * @param array $data
     */
    public function __construct(
        Context $context,
        PurchasesDataProvider $purchasesDataProvider,
        BackendData $backendData,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->purchasesDataProvider = $purchasesDataProvider;
        $this->backendData           = $backendData;
    }

    /**
     * Get input type
     *
     * @return string
     */
    public function getInputType()
    {
        return 'multiselect';
    }

    /**
     * Get value element type
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'text';
    }

    /**
     * Get attribute element
     *
     * @return AbstractCondition
     */
    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);

        return $element;
    }

    /**
     * Load attribute options
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $this->setAttributeOption(
            [
                'purchased_sku' => __('Purchased SKU(s)')
            ]
        );

        return $this;
    }

    /**
     * Get value select options
     *
     * @return array
     */
    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            $this->setData('value_select_options', []);
        }

        return $this->getData('value_select_options');
    }

    /**
     * @return bool
     */
    public function getExplicitApply()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getMappedSqlField()
    {
        return 'e.sku';
    }

    /**
     * Retrieve after element HTML
     *
     * @return string
     */
    public function getValueAfterElementHtml()
    {
        $image = $this->_assetRepo->getUrl('images/rule_chooser_trigger.gif');

        return '<a href="javascript:void(0)" class="rule-chooser-trigger"><img src="' .
            $image .
            '" alt="" class="v-middle rule-chooser-trigger" title="' .
            __(
                'Open Chooser'
            ) . '" /></a>';
    }

    /**
     * Retrieve value element chooser URL
     *
     * @return string
     */
    public function getValueElementChooserUrl()
    {
        $objectManager    = \Magento\Framework\App\ObjectManager::getInstance();
        $productCondition = $objectManager->create(\Magento\CatalogRule\Model\Rule\Condition\Product::class);
        $productCondition->setAttribute('sku');

        if ($this->getJsFormObject()) {
            $productCondition->setJsFormObject($this->getJsFormObject());
        }

        return $productCondition->getValueElementChooserUrl();
    }

    /**
     * @param AbstractModel $model
     * @return bool
     * @throws NoSuchEntityException
     */
    public function validate(AbstractModel $model)
    {
        $customerId = $model->getCustomerId();

        if (!$customerId) {
            return false;
        }

        $attributeValue = $this->purchasesDataProvider->getPurchasedSkuByCustomerId($customerId);

        if (empty($attributeValue)) {
            return false;
        }

        return $this->validateAttribute($attributeValue);
    }
}