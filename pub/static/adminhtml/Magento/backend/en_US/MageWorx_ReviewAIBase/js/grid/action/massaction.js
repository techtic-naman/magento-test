define([
    'underscore',
    'Magento_Ui/js/grid/massactions',
    'uiRegistry',
    'mageUtils'
], function (_, Massactions, registry, utils) {
    'use strict';

    return Massactions.extend({
        defaults: {
            listens: {}
        },

        callbackWithStoreId: function (action, data) {
            let itemsType = data.excludeMode ? 'excluded' : 'selected',
                selections = {};

            selections[itemsType] = data[itemsType];

            if (!selections[itemsType].length) {
                selections[itemsType] = false;
            }

            if (action.additional && _.isObject(action.additional)) {
                _.each(action.additional, function (additionalItem) {
                    if (additionalItem.provider && additionalItem.dataScope) {
                        let providerValue = registry.get(additionalItem.provider);
                        if (providerValue && providerValue[additionalItem.dataScope]) {
                            selections[additionalItem.dataScope] = providerValue[additionalItem.dataScope];
                        }
                    }
                });
            }

            _.extend(selections, data.params || {});

            console.log('Selections: ', selections);

            utils.submit({
                url: action.url,
                data: selections
            });
        }

    });
});
