/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiRegistry',
    'underscore',
    'Magento_Ui/js/form/element/checkbox-set',
    'jquery'
], function (registry, _, CheckboxSet, $) {
    'use strict';

    return CheckboxSet.extend({

        /**
         * Apply Visibility Filter
         */
        applyVisibilityFilter: function () {

            var displayMode = registry.get('index = ' + this.indexies.display_mode);
            var eventType   = registry.get('index = ' + this.indexies.event_type);

            var str = displayMode.value() + '-' + eventType.value() + '-';

            $('[data-index="' + this.index +'"] .admin__field.admin__field-option').each(function(i, elem) {
                if ($(elem).children('input').val().startsWith(str)) {
                    $(elem).show();
                } else {
                    $(elem).hide();
                }
            });
        }
    });
});
