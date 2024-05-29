define([
    'underscore',
    'jquery',
    'mage/translate',
    'Magento_Ui/js/modal/alert',
    'uiRegistry',
    'mage/url'
], function (_, $, $t, alert, registry, urlBuilder) {
    'use strict';

    return {
        generate: function (ajaxUrl, payload) {
            $.ajax({
                url: urlBuilder.build(ajaxUrl),
                type: 'POST',
                dataType: 'json',
                data: payload,
                showLoader: true,
                success: function (response) {
                    if (response.error) {
                        alert({
                            content: response.message
                        });
                    } else if (response.answer) {
                        $('#answer').val(response.answer);
                    }
                }.bind(this),
                error: function (xhr, status, errorThrown) {
                    alert({
                        content: $t('Unable to process your request due to unknown error. ' +
                            'Please refresh the page and try again. If the issue persists, ' +
                            'kindly contact Mageworx support for assistance.')
                    });
                }
            });
        }
    };
});
