/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiRegistry',
    'underscore',
    'Magento_Ui/js/modal/modal-component',
    'jquery'
], function (registry, _, ModalComponent, $) {
    'use strict';

    return ModalComponent.extend({
        defaults: {
            getTemplateDataUrl: ''
        },

        /**
         * Load Template
         */
        loadTemplate: function () {

            var template = registry.get('index = ' + this.indexies.template);
            var theme    = registry.get('index = ' + this.indexies.theme);
            var accent   = registry.get('index = ' + this.indexies.accent);
            var preview  = registry.get('index = ' + this.indexies.preview);

            var identifier  = template.value();

            if (identifier) {
                var self = this;

                $.ajax({
                    url: this.getTemplateDataUrl,
                    showLoader: true,
                    data: {form_key: window.FORM_KEY, 'identifier': identifier},
                    type: "POST",
                    dataType : 'json',
                    success: function(result) {
                        var templateTheme  = result.theme;
                        var templateAccent = result.accent;

                        if (!_.isUndefined(templateTheme) && !_.isUndefined(templateAccent)) {
                            theme.value(templateTheme);
                            accent.value(templateAccent);
                            preview.value(templateTheme + '-' + templateAccent);

                            self.closeModal();
                        } else if (!_.isUndefined(result.error)) {
                            alert(result.error);
                        }
                    }
                });
            }
        }
    });
});
