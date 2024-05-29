define([
    'Magento_Ui/js/form/element/select',
    'uiRegistry'
], function (Select, registry) {
    'use strict';

    return Select.extend({
        defaults: {
            listens: {
                'value': 'onStoreChange'
            }
        },

        onStoreChange: function (value) {
            console.log("Selected store_id:", value);
            let provider = this.source;

            provider.set('params.store_id', value);
            this.resetSelectionIds();

            provider.trigger('reload');
        },

        resetSelectionIds: function () {
            let selectionIdsComponent =
                registry.get('review_summary_listing.review_summary_listing.mageworx_reviewai_columns.ids');

            if (selectionIdsComponent) {
                selectionIdsComponent.deselectAll();
            }
        }
    });
});
