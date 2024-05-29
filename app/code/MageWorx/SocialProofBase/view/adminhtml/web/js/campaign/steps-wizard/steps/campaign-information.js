/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'uiComponent',
    'jquery',
    'underscore',
    'uiRegistry'
], function (Component, $, _, uiRegistry) {
    'use strict';

    return Component.extend({
        defaults: {
            form: '',
            fieldset: '',
            notificationMessage: {
                text: null,
                error: null
            },
            nextLabelText: $.mage.__('Save')
        },

        /**
         * Render.
         */
        render: function () {
            var navTabs = $('.ui-tabs');

            if (navTabs) {
                navTabs.show();
            }
        },

        /**
         * Force.
         */
        force: function () {
            var form = uiRegistry.get(this.form);

            if (!_.isUndefined(form)) {
                var redirect = '*/*/index';

                form.save(redirect);
            }
        },

        /**
         * Back.
         */
        back: function () {
            var navTabs = $('.ui-tabs');

            if (navTabs) {
                navTabs.hide();
            }
        },

        /**
         * After show.
         */
        afterShow: function () {
            var tab = uiRegistry.get(this.form + '.' + this.fieldset);

            if (!_.isUndefined(tab)) {
                tab.activate();
            }
        }
    });
});
