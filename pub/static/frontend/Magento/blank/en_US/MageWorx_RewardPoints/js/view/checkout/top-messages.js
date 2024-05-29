/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'uiComponent',
    'ko',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils',
    'underscore'
], function (Component, ko, quote, priceUtils, _) {
    'use strict';

    var rewardpointsConfig = window.checkoutConfig.payment.mageworx_rewardpoints,
        totals = quote.getTotals(),
        pointsAmount = ko.observable(null),
        currencyAmount = ko.observable(null),
        isApplied = ko.observable(null),
        message = ko.observable(null),
        messageTemplate = rewardpointsConfig.checkoutUpcomingMessage,
        pointToCurrencyRate = rewardpointsConfig.rate;

    return Component.extend({

        defaults: {
            template: 'MageWorx_RewardPoints/checkout/top-messages'
        },

        initObservable: function () {
            this._super();

            quote.totals.subscribe(function (totals) {

                var amount = 0;
                var currency = 0;

                if (!_.isEmpty(totals['extension_attributes']['mw_earn_points_data'])) {

                    try {
                        var pointsData = JSON.parse(totals['extension_attributes']['mw_earn_points_data']);

                        for (var i in pointsData) {
                            amount += parseFloat(pointsData[i]);
                            currency = priceUtils.formatPrice(amount * pointToCurrencyRate, quote.getPriceFormat());
                        }

                    } catch (e) {
                        console.log(e);
                    }
                }

                pointsAmount(amount);
                currencyAmount(currency);
                isApplied(parseFloat(pointsAmount()) > 0);

                message(
                    messageTemplate.replace('[p]', '<strong>' + pointsAmount() + '</strong>').replace('[c]', '<strong>' + currencyAmount() + '</strong>')
                );
            });

            return this;
        },

        /**
         * Applied flag
         */
        isApplied: isApplied,

        pointsAmount: pointsAmount,

        currencyAmount: currencyAmount,

        message: message,

        /**
         * @returns {boolean}
         */
        isAvailable: function () {
            return !!rewardpointsConfig.isDisplayCheckoutUpcomingMessage;
        }
    });
});
