/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiRegistry',
    'Magento_Ui/js/form/components/fieldset',
    'underscore',
    'jquery'
], function (uiRegistry, Fieldset, _, $) {
    'use strict';

    return Fieldset.extend({

        /**
         * Show element.
         *
         * @returns {Fieldset}
         */
        show: function () {
            this.visible(true);

            return this;
        },

        /**
         * Hide element.
         *
         * @returns {Fieldset}
         */
        hide: function () {
            this.visible(false);

            return this;
        }
    });
});
