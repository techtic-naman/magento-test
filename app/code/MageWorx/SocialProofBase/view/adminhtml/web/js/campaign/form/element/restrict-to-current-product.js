/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'uiRegistry',
    'Magento_Ui/js/form/element/single-checkbox',
    'underscore',
    'jquery'
], function (uiRegistry, SingleCheckbox, _, $) {
    'use strict';

    return SingleCheckbox.extend({

        /**
         * @param {String} value
         */
        changeVisibilityOfDependentTabs(value) {
            var navTabsObj = uiRegistry.get('index = ' + this.indexies.navTabsObj);

            if (!_.isUndefined(navTabsObj) && navTabsObj.rendered) {
                this._changeVisibilityOfDependentTabs(value);
            } else {
                $('body').on(
                    'campaignNavTabsRender',
                    '.ui-tabs',
                    this._changeVisibilityOfDependentTabs.bind(this, value)
                );
            }
        },

        _changeVisibilityOfDependentTabs(value) {
            var tabs = this.indexies.tabs;

            if (!_.isUndefined(tabs)) {

                for (let key in tabs) {
                    var fieldset = uiRegistry.get('index = ' + tabs[key]);
                    var navTab   = $('#tab_' + tabs[key]);

                    if (!_.isUndefined(fieldset) && navTab) {

                        if (fieldset.visibleValue === value) {
                            navTab.show();
                        } else {
                            navTab.hide();
                        }
                    }
                }
            }
        }
    });
});
