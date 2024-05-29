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
            template: 'MageWorx_ReviewAIBase/generate-button',
            ajaxUrl: 'mageworx_reviewai/reviewsummary/GenerateForProduct',
            formKeySelector: 'input[name="form_key"]',
            exports: {
                result: '${$.provider}:data.product.summary_data'
            }
        },

        observableFields: ['result'],

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
                    } else if (response.result) {
                        this.result(response.result);
                        this.updateResultInTargetComponent(response.result);
                        alert({
                            content: $t('The review summary was generated and saved successfully. ' +
                                'If you want to make any changes to the text, you can do so now, ' +
                                'but don\'t forget to save the product when you\'re done.')
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

        /**
         * Update the result in the target component. Trigger TinyMCE update if necessary.
         *
         * @param result
         */
        updateResultInTargetComponent: function (result) {
            let targetComponent = registry.get('index = summary_data');
            if (!targetComponent) {
                return;
            }

            let editorId = targetComponent.wysiwygId;
            if (editorId && tinyMCE) {
                let editorInstance = tinyMCE.get(editorId);
                if (editorInstance) {
                    editorInstance.setContent(result);
                }
            }

            if (typeof targetComponent.setData === 'function') {
                targetComponent.setData(result);
            }
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
