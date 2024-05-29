<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\RewardPoints\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * Config Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const SCOPE_CODE_GLOBAL  = 'global';
    const SCOPE_CODE_WEBSITE = 'website';

    /**#@+
     * Config paths to main settings
     */
    const ENABLE                          = 'mageworx_rewardpoints/main/enable';
    const CUSTOMER_POINTS_BLOCK           = 'mageworx_rewardpoints/main/block_for_customer_points';
    const APPLY_POINTS_TO                 = 'mageworx_rewardpoints/main/apply_points_to';
    const POINT_TO_CURRENCY_RATE          = 'mageworx_rewardpoints/main/point_exchange_rate';
    const ALLOWED_CUSTOMER_GROUPS         = 'mageworx_rewardpoints/main/allowed_customer_groups';
    const ALLOWED_CUSTOM_POINTS_AMOUNT    = 'mageworx_rewardpoints/main/allow_custom_amount';
    const RETURN_POINTS_ON_REFUND         = 'mageworx_rewardpoints/main/is_return_points_on_refund';
    const RETURN_POINTS_ON_CANCELLATION   = 'mageworx_rewardpoints/main/is_return_points_on_cancellation';

    /**#@+
     * Config paths to marketing settings
     */
    const DISPLAY_PRODUCT_PROMISE_MESSAGE        = 'mageworx_rewardpoints/marketing/display_product_reward_promise_message';
    const PRODUCT_PROMISE_MESSAGE                = 'mageworx_rewardpoints/marketing/product_reward_promise_message';
    const DISPLAY_CATEGORY_PROMISE_MESSAGE       = 'mageworx_rewardpoints/marketing/display_category_reward_promise_message';
    const CATEGORY_PROMISE_MESSAGE               = 'mageworx_rewardpoints/marketing/category_reward_promise_message';
    const DISPLAY_UPCOMING_MESSAGE               = 'mageworx_rewardpoints/marketing/display_upcoming_points_message';
    const UPCOMING_POINTS_MESSAGE                = 'mageworx_rewardpoints/marketing/upcoming_points_message';
    const DISPLAY_CART_UPCOMING_MESSAGE          = 'mageworx_rewardpoints/marketing/display_cart_upcoming_points_message';
    const CART_UPCOMING_POINTS_MESSAGE           = 'mageworx_rewardpoints/marketing/cart_upcoming_points_message';
    const DISPLAY_CHECKOUT_UPCOMING_MESSAGE      = 'mageworx_rewardpoints/marketing/display_checkout_upcoming_points_message';
    const CHECKOUT_UPCOMING_POINTS_MESSAGE       = 'mageworx_rewardpoints/marketing/checkout_upcoming_points_message';
    const DISPLAY_MINICART_POINT_BALANCE_MESSAGE = 'mageworx_rewardpoints/marketing/display_minicart_point_balance_message';
    const MINICART_POINT_BALANCE_MESSAGE         = 'mageworx_rewardpoints/marketing/minicart_point_balance_message';
    const MINICART_EMPTY_POINT_BALANCE_MESSAGE   = 'mageworx_rewardpoints/marketing/minicart_empty_point_balance_message';
    const CUSTOM_POINTS_INPUT_PLACEHOLDER        = 'mageworx_rewardpoints/marketing/custom_amount_input_placeholder';
    const ENABLE_RSS                             = 'mageworx_rewardpoints/marketing/rss_enable';
    const USE_COMMENT_WRAPPER                    = 'mageworx_rewardpoints/marketing/use_comment_wrapper';

    /**#@+
     * Config paths to expiration date settings
     */
    const ENABLE_EXPIRATION_DATE              = 'mageworx_rewardpoints/expiration_date/enable';
    const DEFAULT_EXPIRATION_PERIOD_IN_DAYS   = 'mageworx_rewardpoints/expiration_date/default_period_in_days';
    const UPDATE_DATES_CONDITION              = 'mageworx_rewardpoints/expiration_date/update_dates_condition';
    const ENABLE_EXPIRATION_DATE_NOTIFICATION = 'mageworx_rewardpoints/expiration_date/enable_notification';
    const DAYS_BEFORE_EXPIRE_NOTIFICATION     = 'mageworx_rewardpoints/expiration_date/days_before_notification';

    /**#@+
     * Config paths to notification settings
     */
    const EMAIL_SENDER                   = 'mageworx_rewardpoints/notification/sender_email_identity';
    const EMAIL_TEMPLATE_FROM_RULES      = 'mageworx_rewardpoints/notification/default_email_template_update_points';
    const EMAIL_TEMPLATE_FROM_ADMIN      = 'mageworx_rewardpoints/notification/email_template_update_points_by_admin';
    const EMAIL_TEMPLATE_EXPIRATION_DATE = 'mageworx_rewardpoints/notification/email_template_expiration_date';

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * Data constructor.
     *
     * @param Context $context
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Escaper $escaper
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->escaper         = $escaper;
    }

    /**
     * @param null|int $storeId
     * @return bool
     */
    public function isEnable($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::ENABLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null|int $storeId
     * @return bool
     */
    public function isRssEnable($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::ENABLE_RSS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param \Magento\Customer\Model\Customer|null $customer
     * @param null $storeId
     * @return bool
     */
    public function isEnableForCustomer($customer = null, $storeId = null)
    {
        if (!$this->isEnable($storeId)) {
            return false;
        }

        if ($customer && $customer->getId()) {
            $customerGroupId = $customer->getGroupId();
        } else {
            if ($this->customerSession->getCustomerId()) {
                $customerGroupId = $this->customerSession->getCustomerGroupId();
            }
        }

        if (!empty($customerGroupId)) {
            $validGroups = $this->getAllowedCustomerGroups($storeId);

            return in_array($customerGroupId, $validGroups);
        }

        return false;
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getCustomerPointsBlockId($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::CUSTOMER_POINTS_BLOCK,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int|null $storeId
     * @return array
     */
    public function getApplyForList($storeId = null)
    {
        $string = (string)$this->scopeConfig->getValue(
            self::APPLY_POINTS_TO,
            ScopeInterface::SCOPE_WEBSITE,
            $storeId
        );

        $arrayRaw = array_map('trim', explode(',', $string));

        return array_filter($arrayRaw);
    }

    /**
     * @param int|null $websiteId
     * @return double
     */
    public function getPointToCurrencyRate($websiteId = null)
    {
        $value = $this->scopeConfig->getValue(
            self::POINT_TO_CURRENCY_RATE,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );

        $result = (double)$value;

        if (!$result) {

            $message = 'MageWorx_RewardPoints: ' .
                __('the points exchange rate has incorrect format.') . ' ' .
                __('Check the configuration settings.');

            $this->_logger->log(\Psr\Log\LogLevel::ERROR, $message);
        }

        return (double)$value;
    }

    /**
     * @param int|null $storeId
     * @return double
     */
    public function getCurrencyToPointRate($storeId = null)
    {
        $rate = $this->getPointToCurrencyRate($storeId);

        return $rate ? 1 / $rate : 0;
    }

    /**
     * @param int|null $storeId
     * @return array
     */
    public function getAllowedCustomerGroups($storeId = null)
    {
        $string = (string)$this->scopeConfig->getValue(
            self::ALLOWED_CUSTOMER_GROUPS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $arrayRaw = array_map('trim', explode(',', $string));

        return array_filter($arrayRaw);
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isAllowedCustomPointsAmount($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::ALLOWED_CUSTOM_POINTS_AMOUNT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function getIsReturnPointsOnRefund($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::RETURN_POINTS_ON_REFUND,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function getIsReturnSpentPointOnCancellation($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::RETURN_POINTS_ON_CANCELLATION,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null $storeId
     * @return string
     */
    public function getEmailSender($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::EMAIL_SENDER,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param null|int $storeId
     * @return string
     */
    public function getRuleEmailTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::EMAIL_TEMPLATE_FROM_RULES,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null|int $storeId
     * @return string
     */
    public function getAdminEmailTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::EMAIL_TEMPLATE_FROM_ADMIN,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null|int $storeId
     * @return string
     */
    public function getNotificationEmailTemplateId($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::EMAIL_TEMPLATE_EXPIRATION_DATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if display reward promise message on the product page
     *
     * @param null|int $storeId
     * @return bool
     */
    public function isDisplayPromiseMessageOnProduct($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::DISPLAY_PRODUCT_PROMISE_MESSAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve reward promise message for the product page
     *
     * @param null|int $storeId
     * @return string
     */
    public function getPromiseMessageForProduct($storeId = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::PRODUCT_PROMISE_MESSAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if display reward promise message on the category page
     *
     * @param null|int $storeId
     * @return bool
     */
    public function isDisplayPromiseMessageOnCategory($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::DISPLAY_CATEGORY_PROMISE_MESSAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve reward promise message for the category page
     *
     * @param null|int $storeId
     * @return string
     */
    public function getPromiseMessageForCategory($storeId = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::CATEGORY_PROMISE_MESSAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if display upcoming points message for header is possible
     *
     * @param null|int $storeId
     * @return bool
     */
    public function isDisplayUpcomingPoints($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::DISPLAY_UPCOMING_MESSAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve upcoming points message for header
     *
     * @param null|int $storeId
     * @return string
     */
    public function getUpcomingPointsMessage($storeId = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::UPCOMING_POINTS_MESSAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null|int $storeId
     * @return bool
     */
    public function isDisplayMinicartPointBalanceMessage($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::DISPLAY_MINICART_POINT_BALANCE_MESSAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null|int $storeId
     * @return string
     */
    public function getMinicartPointBalanceMessage($storeId = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::MINICART_POINT_BALANCE_MESSAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null|int $storeId
     * @return string
     */
    public function getMinicartEmptyPointBalanceMessage($storeId = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::MINICART_EMPTY_POINT_BALANCE_MESSAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int|null $storeId
     * @return string
     */
    public function getCustomPointsInputPlaceholder($storeId = null)
    {
        return $this->escaper->escapeHtml((string)
            $this->scopeConfig->getValue(
                self::CUSTOM_POINTS_INPUT_PLACEHOLDER,
                ScopeInterface::SCOPE_STORE
            )
        );
    }

    /**
     * Check if display upcoming points message for cart is possible
     *
     * @param null|int $storeId
     * @return bool
     */
    public function isDisplayCartUpcomingPoints($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::DISPLAY_CART_UPCOMING_MESSAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve upcoming points message using on cart page
     *
     * @param null|int $storeId
     * @return string
     */
    public function getCartUpcomingPointsMessage($storeId = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::CART_UPCOMING_POINTS_MESSAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if display upcoming points message for checkout is possible
     *
     * @param null|int $storeId
     * @return bool
     */
    public function isDisplayCheckoutUpcomingPoints($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::DISPLAY_CHECKOUT_UPCOMING_MESSAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve upcoming points message using on checkout page
     *
     * @param null|int $storeId
     * @return string
     */
    public function getCheckoutUpcomingPointsMessage($storeId = null)
    {
        return (string)$this->escaper->escapeHtml(
            $this->scopeConfig->getValue(
                self::CHECKOUT_UPCOMING_POINTS_MESSAGE,
                ScopeInterface::SCOPE_STORE,
                $storeId
            )
        );
    }

    /**
     * @param null|int $websiteId
     * @return bool
     */
    public function isEnableExpirationDate($websiteId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::ENABLE_EXPIRATION_DATE,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * @param null|int $websiteId
     * @return bool
     */
    public function isEnableExpirationDateNotification($websiteId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::ENABLE_EXPIRATION_DATE_NOTIFICATION,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * @param null|int $websiteId
     * @return int
     */
    public function getDefaultExpirationPeriod($websiteId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::DEFAULT_EXPIRATION_PERIOD_IN_DAYS,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * @param null|int $websiteId
     * @return int
     */
    public function isUpdateExpirationDatesIfDefaultPeriodChanged($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            self::UPDATE_DATES_CONDITION,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * @param null|int $websiteId
     * @return int
     */
    public function getDaysBeforeExpirationDateForNotification($websiteId = null)
    {
        return (int)$this->scopeConfig->getValue(
            self::DAYS_BEFORE_EXPIRE_NOTIFICATION,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * @return bool
     */
    public function getUseCommentWrapper()
    {
        return $this->scopeConfig->isSetFlag(self::USE_COMMENT_WRAPPER);
    }

    /**
     * @return void
     */
    private function forAutoTranslate()
    {
        $this->__('Enter amount of Reward Points you want to use in this order');
    }
}
