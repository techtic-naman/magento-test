/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'uiRegistry',
    'Magento_Ui/js/form/element/multiselect',
    'underscore',
    'jquery'
], function (uiRegistry, MultiSelect, _, $) {
    'use strict';

    return MultiSelect.extend({

        /**
         * On value change handler.
         *
         * @param {Array} value
         */
        onUpdate: function (value) {
            this.changeVisibilityOfDependentTabs(value);

            return this._super();
        },

        /**
         * Show element.
         */
        show: function () {
            var navTabsObj = uiRegistry.get('index = ' + this.indexies.navTabsObj);
            var value      = this.value();

            if (!_.isUndefined(navTabsObj) && navTabsObj.rendered) {
                this.changeVisibilityOfDependentTabs(value);
            } else {
                $('body').on(
                    'campaignNavTabsRender',
                    '.ui-tabs',
                    this.changeVisibilityOfDependentTabs.bind(this, value)
                );
            }

            return this._super();
        },

        /**
         * Hide element.
         */
        hide: function () {
            var navTabsObj = uiRegistry.get('index = ' + this.indexies.navTabsObj);

            if (!_.isUndefined(navTabsObj) && navTabsObj.rendered) {
                this.hideDependentTabs();
            } else {
                $('body').on(
                    'campaignNavTabsRender',
                    '.ui-tabs',
                    this.hideDependentTabs.bind(this)
                );
            }

            return this._super();
        },

        /**
         * @param {Array} value
         */
        changeVisibilityOfDependentTabs(value) {
            var tabs = this.indexies.tabs;

            if (!_.isUndefined(tabs)) {
                var restrictToCurProductObj = uiRegistry.get('index = ' + this.indexies.restrict_to_current_product);
                var restrictToCurProduct    = restrictToCurProductObj.value() === '1';

                for (let key in tabs) {
                    var fieldset = uiRegistry.get('index = ' + tabs[key]);
                    var navTab   = $('#tab_' + tabs[key]);

                    if (!_.isUndefined(fieldset) && navTab) {

                        if ($.inArray(fieldset.visibleValue, value) !== -1 && !restrictToCurProduct) {
                            navTab.show();
                        } else {
                            navTab.hide();
                        }
                    }
                }
            }
        },

        /**
         * Hide dependent tabs.
         */
        hideDependentTabs() {
            var tabs = this.indexies.tabs;

            if (!_.isUndefined(tabs)) {

                for (let key in tabs) {
                    var navTab = $('#tab_' + tabs[key]);

                    if (navTab) {
                        navTab.hide();
                    }
                }
            }
        }
    });
});
