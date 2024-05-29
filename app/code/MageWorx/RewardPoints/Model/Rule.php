<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace MageWorx\RewardPoints\Model;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Quote\Model\Quote\Address;

/**
 * Reward Points Rule data model
 *
 * @method string getName()
 * @method \MageWorx\RewardPoints\Model\Rule setName(string $value)
 * @method string getDescription()
 * @method \MageWorx\RewardPoints\Model\Rule setDescription(string $value)
 * @method \MageWorx\RewardPoints\Model\Rule setFromDate(string $value)
 * @method \MageWorx\RewardPoints\Model\Rule setToDate(string $value)
 * @method \MageWorx\RewardPoints\Model\Rule setCustomerGroupIds(string $value)
 * @method int getIsActive()
 * @method \MageWorx\RewardPoints\Model\Rule setIsActive(int $value)
 * @method string getConditionsSerialized()
 * @method \MageWorx\RewardPoints\Model\Rule setConditionsSerialized(string $value)
 * @method string getActionsSerialized()
 * @method \MageWorx\RewardPoints\Model\Rule setActionsSerialized(string $value)
 * @method int getStopRulesProcessing()
 * @method \MageWorx\RewardPoints\Model\Rule setStopRulesProcessing(int $value)
 * @method int getSortOrder()
 * @method \MageWorx\RewardPoints\Model\Rule setSortOrder(int $value)
 * @method string getCalculationType()
 * @method \MageWorx\RewardPoints\Model\Rule setCalculationType(string $value)
 * @method string getSimpleAction()
 * @method \MageWorx\RewardPoints\Model\Rule setSimpleAction(string $value)
 * @method float getPointsAmount()
 * @method \MageWorx\RewardPoints\Model\Rule setPointsAmount(float $value)
 * @method float getDiscountQty()
 * @method \MageWorx\RewardPoints\Model\Rule setDiscountQty(float $value)
 * @method float getPointsStep()
 * @method \MageWorx\RewardPoints\Model\Rule setPointsStep(float $value)
 * @method float getPointStage()
 * @method \MageWorx\RewardPoints\Model\Rule setPointStage(float $value)
 * @method int getTimesUsed()
 * @method \MageWorx\RewardPoints\Model\Rule setTimesUsed(int $value)
 * @method int getIsRss()
 * @method \MageWorx\RewardPoints\Model\Rule setIsRss(int $value)
 * @method string getWebsiteIds()
 * @method \MageWorx\RewardPoints\Model\Rule setWebsiteIds(string $value)
 * @method int getRuleId()
 * @method \MageWorx\RewardPoints\Model\Rule setRuleId(int $ruleId)
 * @method bool getIsAllowNotification()
 * @method \MageWorx\RewardPoints\Model\Rule setIsAllowNotification(bool $value)
 * @method string getEmailTemplateId()
 * @method \MageWorx\RewardPoints\Model\Rule setEmailTemplateId(string $emailTemplateId)
 *
 * @property \MageWorx\RewardPoints\Model\ResourceModel\Rule $_resource
 */
class Rule extends \Magento\Rule\Model\AbstractModel
{
    /** @see \MageWorx\RewardPoints\Model\Source\Event */
    const ORDER_PLACED_EARN_EVENT                = 'order_placed_earn';
    const CUSTOMER_BIRTHDAY_EVENT                = 'customer_birthday';
    const CUSTOMER_REGISTRATION_EVENT            = 'customer_registration';
    const CUSTOMER_REVIEW_EVENT                  = 'customer_review';
    const CUSTOMER_INACTIVITY_EVENT              = 'customer_inactivity';
    const CUSTOMER_NEWSLETTER_SUBSCRIPTION_EVENT = 'newsletter_subscription';

    /** @see \MageWorx\RewardPoints\Model\Source\GivePoints */
    const CALCULATION_METHOD_FIXED                     = 'by_fixed_action';
    const CALCULATION_METHOD_SPEND_Y_GET_X             = 'spend_y_get_x_action';
    const CALCULATION_METHOD_SPEND_Y_MORE_THAN_Z_GET_X = 'spend_y_more_than_z_get_x_action';
    const CALCULATION_METHOD_BUY_Y_GET_X               = 'buy_y_get_x_action';
    const CALCULATION_METHOD_BUY_Y_MORE_THAN_Z_GET_X   = 'buy_y_more_than_z_get_x_action';

    /**
     * @see \MageWorx\RewardPoints\Model\Source\CalculationTypes
     * @see \MageWorx\RewardPoints\Setup\InstallSchema - 'fixed' is used as default value
     */
    const CALCULATION_TYPE_FIXED   = 'fixed';
    const CALCULATION_TYPE_PERCENT = 'percent';

    const STATUS_DISABLED = 0;
    const STATUS_ENABLED  = 1;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'mageworx_rewardpoints_rule';

    /**
     * @var string
     */
    protected $_eventObject = 'rule';

    /**
     * Store already validated addresses and validation results
     *
     * @var array
     */
    protected $validatedAddresses = [];

    /**
     * @var \MageWorx\RewardPoints\Model\Rule\Condition\CombineFactory
     */
    protected $condCombineFactory;

    /**
     * @var \MageWorx\RewardPoints\Model\Rule\Condition\Product\CombineFactory
     */
    protected $condProdCombineFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Rule constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \MageWorx\RewardPoints\Model\Rule\Condition\CombineFactory $condCombineFactory
     * @param \MageWorx\RewardPoints\Model\Rule\Condition\Product\CombineFactory $condProdCombineFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     * @param ExtensionAttributesFactory|null $extensionFactory
     * @param AttributeValueFactory|null $customAttributeFactory
     * @param \Magento\Framework\Serialize\Serializer\Json|null $serializer
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \MageWorx\RewardPoints\Model\Rule\Condition\CombineFactory $condCombineFactory,
        \MageWorx\RewardPoints\Model\Rule\Condition\Product\CombineFactory $condProdCombineFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \MageWorx\RewardPoints\Model\ResourceModel\Rule $resource,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        ExtensionAttributesFactory $extensionFactory = null,
        AttributeValueFactory $customAttributeFactory = null,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    ) {
        $this->condCombineFactory     = $condCombineFactory;
        $this->condProdCombineFactory = $condProdCombineFactory;
        $this->storeManager           = $storeManager;
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $localeDate,
            $resource,
            $resourceCollection,
            $data,
            $extensionFactory,
            $customAttributeFactory,
            $serializer
        );
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\MageWorx\RewardPoints\Model\ResourceModel\Rule::class);
        $this->setIdFieldName('rule_id');
    }

    /**
     * Initialize rule model data from array.
     * Set store labels if applicable.
     *
     * @param array $data
     * @return $this
     */
    public function loadPost(array $data)
    {
        parent::loadPost($data);

        if (isset($data['store_labels'])) {
            $this->setStoreLabels($data['store_labels']);
        }

        return $this;
    }

    /**
     * Get rule condition combine model instance
     *
     * @return \MageWorx\RewardPoints\Model\Rule\Condition\Combine
     */
    public function getConditionsInstance(): \MageWorx\RewardPoints\Model\Rule\Condition\Combine
    {
        return $this->condCombineFactory->create();
    }

    /**
     * Get rule condition product combine model instance
     *
     * @return \MageWorx\RewardPoints\Model\Rule\Condition\Product\Combine
     */
    public function getActionsInstance(): \MageWorx\RewardPoints\Model\Rule\Condition\Product\Combine
    {
        return $this->condProdCombineFactory->create();
    }

    /**
     * Get reward rule customer group Ids
     *
     * @return array
     */
    public function getCustomerGroupIds(): array
    {
        if (!$this->hasCustomerGroupIds()) {
            $customerGroupIds = $this->_resource->getCustomerGroupIds($this->getId());
            $this->setData('customer_group_ids', (array)$customerGroupIds);
        }

        return $this->_getData('customer_group_ids');
    }

    /**
     * @param null|int $store
     * @return bool|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreLabel(int $store = null)
    {
        $storeId = $this->storeManager->getStore($store)->getId();
        $labels  = (array)$this->getStoreLabels();

        if (isset($labels[$storeId])) {
            return $labels[$storeId];
        } elseif (isset($labels[0]) && $labels[0]) {
            return $labels[0];
        }

        return false;
    }

    /**
     * @return array
     */
    public function getStoreLabels(): array
    {
        if (!$this->hasStoreLabels()) {
            $labels = $this->_resource->getStoreLabels($this->getId());
            $this->setStoreLabels($labels);
        }

        return $this->_getData('store_labels');
    }

    /**
     * @return string
     */
    public function getFromDate(): string
    {
        return (string)$this->getData('from_date');
    }

    /**
     * @return string
     */
    public function getToDate(): string
    {
        return (string)$this->getData('to_date');
    }

    /**
     * @param double $points
     * @return $this
     */
    public function setCalculatedPoints($points): Rule
    {
        return $this->setData('calculated_points', $points);
    }

    /**
     * @return double|null
     */
    public function getCalculatedPoints()
    {
        return $this->getData('calculated_points') ? (float)$this->getData('calculated_points') : null;
    }

    /**
     * Check cached validation result for specific address
     *
     * @param Address $address
     * @return bool
     */
    public function hasIsValidForAddress(Address $address): bool
    {
        $addressId = $this->_getAddressId($address);

        return isset($this->validatedAddresses[$addressId]) ? true : false;
    }

    /**
     * Set validation result for specific address to results cache
     *
     * @param Address $address
     * @param bool $validationResult
     * @return $this
     */
    public function setIsValidForAddress(Address $address, bool $validationResult): Rule
    {
        $addressId                            = $this->_getAddressId($address);
        $this->validatedAddresses[$addressId] = $validationResult;

        return $this;
    }

    /**
     * Get cached validation result for specific address
     *
     * @param Address $address
     * @return bool
     */
    public function getIsValidForAddress(Address $address): bool
    {
        $addressId = $this->_getAddressId($address);

        return isset($this->validatedAddresses[$addressId]) ? $this->validatedAddresses[$addressId] : false;
    }

    /**
     * @param Address $address
     * @return int
     */
    private function _getAddressId(Address $address): int
    {
        if ($address instanceof Address) {
            return (int)$address->getId();
        }

        return (int)$address;
    }

    /**
     * @param string $formName
     * @return string
     */
    public function getConditionsFieldSetId(string $formName = ''): string
    {
        return $formName . 'rule_conditions_fieldset_' . $this->getId();
    }

    /**
     * @param string $formName
     * @return string
     */
    public function getActionsFieldSetId(string $formName = ''): string
    {
        return $formName . 'rule_actions_fieldset_' . $this->getId();
    }

    /**
     * @return bool
     */
    public function hasActionEmptyConditions(): bool
    {
        $actionsAsArray = $this->getActions()->asArray();

        if (empty($actionsAsArray['conditions'])) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave()
    {
        $this->roundStepAndStageIfCalculationUsingQty();
        $this->resetStepAndStageIfPossible();
        parent::beforeSave();

        return $this;
    }

    /**
     * Reset Step or/and Stage properties if simple_action (Give Points) don't use them.
     *
     * @return $this
     */
    protected function resetStepAndStageIfPossible(): Rule
    {
        switch ($this->getSimpleAction()) {
            case Rule::CALCULATION_METHOD_FIXED:
                $this->setPointsStep(0);
            // no break
            case Rule::CALCULATION_METHOD_SPEND_Y_GET_X:
            case Rule::CALCULATION_METHOD_BUY_Y_GET_X:
                $this->setPointStage(0);
                break;
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function roundStepAndStageIfCalculationUsingQty(): Rule
    {
        switch ($this->getSimpleAction()) {
            case Rule::CALCULATION_METHOD_BUY_Y_GET_X:
            case Rule::CALCULATION_METHOD_BUY_Y_MORE_THAN_Z_GET_X:
                $this->setPointsStep((int)$this->getPointsStep());
                $this->setPointStage((int)$this->getPointStage());
                break;
        }

        return $this;
    }
}
