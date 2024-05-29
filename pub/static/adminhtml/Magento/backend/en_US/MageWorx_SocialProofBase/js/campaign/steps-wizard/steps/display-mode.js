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
        },

        /** @inheritdoc */
        initialize: function () {
            this._super();

            var navTab = $('#tab_' + this.fieldset);

            if (navTab) {
                navTab.hide();
            }

            return this;
        },

        /**
         * Render.
         */
        render: function () {
        },

        /**
         * Force.
         */
        force: function () {
        },

        /**
         * Back.
         */
        back: function () {
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
