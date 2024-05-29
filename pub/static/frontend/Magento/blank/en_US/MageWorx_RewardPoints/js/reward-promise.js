/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'mage/translate',
    'mage/storage',
    'Magento_Customer/js/model/customer'
], function ($, _, $t, storage, customer) {
    'use strict';

    /**
     * RewardPromise constructor.
     *
     * @param config
     * @param element
     */
    var RewardPromise = function (config, element) {
        this.config = config;
        this.element = element;

        this.init = function () {
            if ($('#mw-reward-promise').length) {
                this.updateContent();
            }
            return this;
        };

        this.updateContent = function () {
            var self = this;
            let ids = [this.config.productId];
            let serviceUrl = this.config.serviceUrl;

            storage.post(
                serviceUrl,
                JSON.stringify({
                    "product_ids": ids
                }),
                false
            ).done(function (result) {
                self._processSuccess(result);
            }).fail(function (response) {

            }).always(function () {

            });
        };

        this._processSuccess = function (result) {
            var self = this;
            for (let key in result) {
                let rewardPromise = result[key];

                if (rewardPromise.amount > 0) {
                    let selector = '';
                    let message = this.config.message.replace('[p]', '<strong>' + rewardPromise.amount + '</strong>');

                    $('#mwrp-banner__content').replaceWith(message);
                    self.show();
                }
            }
        };

        this.show = function () {
            $(this.element).show();
        };

        this.hide = function () {
            $(this.element).hide();
        };

        this.init();
    };

    return {
        'reward-promise': RewardPromise
    }
});
