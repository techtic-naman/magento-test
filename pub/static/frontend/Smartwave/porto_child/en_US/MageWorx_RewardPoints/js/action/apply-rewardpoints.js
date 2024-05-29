/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

/**
 * Customer store credit(balance) application
 */
define([
    'jquery',
    'mage/url',
    'Magento_Checkout/js/model/error-processor',
    'MageWorx_RewardPoints/js/model/payment/rewardpoints-messages',
    'mage/storage',
    'mage/translate',
    'Magento_Checkout/js/action/get-payment-information',
    'Magento_Checkout/js/model/totals',
    'Magento_Checkout/js/model/full-screen-loader'
], function ($, urlBuilder, errorProcessor, messageContainer, storage, $t, getPaymentInformationAction,
             totals, fullScreenLoader
) {
    'use strict';

    return function (isApplied, pointAmount) {

        if (typeof pointAmount != "undefined") {
            var url = urlBuilder.build('rewardpoints/checkout/apply/amount/' + pointAmount);
        } else {
            var url = urlBuilder.build('rewardpoints/checkout/apply');
        }

        var message_success = $t('The reward points have been successfully applied.'),
            message_error   = $t("The reward points can't be applied");

        fullScreenLoader.startLoader();

        return storage.post(
            url,
            {},
            false
        ).done(function (result) {
            var response;

            try {
                response = $.parseJSON(result);
            } catch (e) {
                response = result;
            }

            if (response && response.result == 'true') {

                var deferred = $.Deferred();

                isApplied(true);

                totals.isLoading(true);
                getPaymentInformationAction(deferred);
                $.when(deferred).done(function () {
                    fullScreenLoader.stopLoader();
                    totals.isLoading(false);
                });

                messageContainer.addSuccessMessage({
                    'message': message_success
                });
            } else {
                totals.isLoading(false);
                fullScreenLoader.stopLoader();
                messageContainer.addErrorMessage({
                    'message': message_error
                });
            }
        }).fail(function (response) {
            fullScreenLoader.stopLoader();
            totals.isLoading(false);
            errorProcessor.process(response, messageContainer);
        }).always(function () {
            fullScreenLoader.stopLoader();
        });
    };
});