/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'uiComponent',
    'ko',
    'Magento_Checkout/js/model/quote',
    'MageWorx_RewardPoints/js/action/apply-rewardpoints',
    'MageWorx_RewardPoints/js/action/cancel-rewardpoints'
], function ($, Component, ko, quote, applyRewardPointsAction, cancelRewardPointsAction) {
    'use strict';

    var rewardpointsConfig = window.checkoutConfig.payment.mageworx_rewardpoints,
        totals = quote.getTotals(),
        pointAmount = ko.observable(null),
        isApplied;

    if (totals()) {
        if (totals()['extension_attributes']['mw_rwrdpoints_amnt'] == "0.0000") {
            // show placeholder
            pointAmount('');
        } else {
            pointAmount(totals()['extension_attributes']['mw_rwrdpoints_amnt']);
        }
    }

    isApplied = ko.observable(parseFloat(pointAmount()) !== 0 && !isNaN(parseFloat(pointAmount())));

    return Component.extend({

        // We set the component's template in LayoutProcessor Block using specific config setting

        pointAmount: pointAmount,

        label: rewardpointsConfig.label,

        observableProperties: [
            'inputPlaceholder'
        ],

        /**
         * Applied flag
         */
        isApplied: isApplied,

        /**
         * @returns {exports}
         */
        initObservable: function () {
            this._super();
            this.observe(this.observableProperties);

            return this;
        },


        /**
         * @return {boolean}
         */
        isAvailable: function () {
            return !!rewardpointsConfig.isAvailable
        },

        /**
         * @returns {boolean}
         */
        isAllowedCustomAmount: function () {
            return !!rewardpointsConfig.isAllowedCustomAmount;
        },

        /**
         * Apply using reward points
         */
        apply: function () {
            applyRewardPointsAction(isApplied);
        },

        /**
         * Apply using reward points
         */
        applyAmount: function () {
            if (this.validate()) {
                applyRewardPointsAction(isApplied, pointAmount());
            }
        },

        /**
         * Cancel using reward points
         */
        cancel: function () {
            cancelRewardPointsAction(isApplied);
        },

        /**
         * Reward points form validation
         *
         * @returns {Boolean}
         */
        validate: function () {
            var $form = $('#rewardpoints-form');

            return $form.validation() && $form.validation('isValid');
        }
    });
});
