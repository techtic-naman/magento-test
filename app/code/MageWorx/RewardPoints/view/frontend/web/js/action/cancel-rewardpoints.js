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
    'Magento_Checkout/js/action/get-payment-information',
    'Magento_Checkout/js/model/totals',
    'mage/translate',
    'Magento_Checkout/js/model/full-screen-loader'
], function ($, urlBuilder, errorProcessor, messageContainer, storage, getPaymentInformationAction, totals, $t,
             fullScreenLoader
) {
    'use strict';

    return function (isApplied) {

        var url = urlBuilder.build('rewardpoints/checkout/cancel'),
            message_success = $t('The reward points have been successfully cancelled.'),
            message_error   = $t("The reward points can't be cancelled");

        messageContainer.clear();
        fullScreenLoader.startLoader();

        return storage.post(
            url,
            false
        ).done(function (result) {

            var response;

            try {
                response = $.parseJSON(result);
            } catch (e) {
                response = result;
            }

            if (response && response['result'] == 'true') {

                var deferred = $.Deferred();

                totals.isLoading(true);
                getPaymentInformationAction(deferred);
                $.when(deferred).done(function () {
                    isApplied(false);
                    totals.isLoading(false);
                    fullScreenLoader.stopLoader();
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
            totals.isLoading(false);
            fullScreenLoader.stopLoader();
            errorProcessor.process(response, messageContainer);
        });
    };
});
