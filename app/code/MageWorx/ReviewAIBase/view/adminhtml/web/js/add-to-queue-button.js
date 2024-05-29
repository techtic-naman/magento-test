define([
    'Magento_Ui/js/form/element/abstract',
    'jquery',
    'mage/translate',
    'Magento_Ui/js/modal/alert',
    'uiRegistry'
], function (Abstract, $, $t, alert, registry) {
    'use strict';

    return Abstract.extend({
        defaults: {
            template: 'MageWorx_ReviewAIBase/add-to-queue-button',
            ajaxUrl: 'mageworx_reviewai/reviewsummary/AddProductToQueue',
            formKeySelector: 'input[name="form_key"]'
        },

        observableFields: [],

        initialize: function () {
            this._super();
            return this;
        },

        initObservable: function () {
            this._super();
            this.observe(this.observableFields);
            return this;
        },

        generateSummary: function () {
            let payload = this.getFormData();

            $.ajax({
                url: this.ajaxUrl,
                type: 'POST',
                dataType: 'json',
                data: payload,
                showLoader: true,
                success: function (response) {
                    if (response.error) {
                        alert({
                            content: response.message
                        });
                    } else if (response.added_to_queue) {
                        alert({
                            content: $t('The product is added to a queue for review summary generation. ' +
                                'Result will be available depending on the queue load. Feel free to close this page.')
                        });
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
        },

        getFormData: function () {
            let formSource = registry.get('product_form.product_form_data_source'),
                formKey = $(this.formKeySelector).val();

            if (!formKey) {
                return null;
            }

            return {
                product_id: formSource.get('data.product.current_product_id'),
                store_id: formSource.get('data.product.current_store_id'),
                form_key: formKey
            };
        }
    });
});
