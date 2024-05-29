/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'jquery',
    'mage/mage',
    'mage/decorate'
], function (Component, customerData, $) {
    'use strict';

    return Component.extend({

        getMessage: function () {
            return customerData.get('cart')()['mageworx_rewardpoints_balance_message'];
        },

        isDisplayed: function () {

            if (customerData.get('cart')) {
                var message = this.getMessage();
                return (typeof message !== 'undefined') && message;
            }
            return false;
        }
    });
});
