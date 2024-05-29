/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/totals'
], function (Component, totals) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'MageWorx_RewardPoints/summary/rewardpoints'
        },
        totals: totals.totals(),

        /**
         * @return {*|String}
         */
        getValue: function () {
            return this.getFormattedPrice(this.getRawValue());
        },

        /**
         * @return {Number}
         */
        getRawValue: function () {
            var segment,
                price = 0;

            if (this.totals) {
                segment = totals.getSegment('mageworx_rewardpoints_spend');

                if (segment) {
                    price = segment.value;
                }
            }

            return price;
        },

        /**
         * Get reward points amount.
         */
        getRewardPointsAmount: function () {
            return totals.totals()['extension_attributes']['mw_rwrdpoints_amnt'];
        },

        /**
         * @return {Boolean}
         */
        isAvailable: function () {
            return this.getRawValue() < 0;
        }
    });
});
