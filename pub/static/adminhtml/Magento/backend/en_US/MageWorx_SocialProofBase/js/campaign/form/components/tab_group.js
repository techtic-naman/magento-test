/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'Magento_Ui/js/form/components/tab_group',
    'underscore',
    'jquery'
], function (TabGroup, _, $) {
    'use strict';

    return TabGroup.extend({
        defaults: {
            rendered: false
        },

        onElementRender() {
            this.rendered = true;

            var event = $.Event("campaignNavTabsRender");

            $('.ui-tabs').trigger(event);
        }
    });
});
