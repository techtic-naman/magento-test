/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'mage/adminhtml/grid'
], function () {
    'use strict';

    return function (config) {
        var selectedCustomers = config.selectedCustomers,
            customers = $H(selectedCustomers),
            gridJsObject = window[config.gridJsObjectName],
            tabIndex = 1000,
            inPromoCustomers = config.namePromoCustomers;

        $(inPromoCustomers).value = Object.toJSON(customers);

        /**
         *
         * @param {Object} grid
         * @param {Object} element
         * @param {Boolean} checked
         */
        function registerCustomers(grid, element, checked) {
            if (checked) {
                customers.set(element.value, "0");
            } else {
                if (element.positionElement) {
                    element.positionElement.disabled = true;
                }
                customers.unset(element.value);
            }
            $(inPromoCustomers).value = Object.toJSON(customers);
            grid.reloadParams = {
                'selected_customers[]': customers.values()
            };
        }

        /**
         *
         * @param {Object} grid
         * @param {String} event
         */
        function customerRowClick(grid, event) {
            var trElement = Event.findElement(event, 'tr'),
                isInput = Event.element(event).tagName === 'INPUT',
                checked = false,
                checkbox = null;

            if (trElement) {
                checkbox = Element.getElementsBySelector(trElement, 'input');

                if (checkbox[0]) {
                    checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                    gridJsObject.setCheckboxChecked(checkbox[0], checked);
                }
            }
        }

        /**
         *
         * @param {Object} grid
         * @param {String} row
         */
        function customersRowInit(grid, row) {
            var checkbox = $(row).getElementsByClassName('checkbox')[0],
                position = $(row).getElementsByClassName('input-text')[0];

            if (checkbox && position) {
                checkbox.positionElement = position;
                position.checkboxElement = checkbox;
                position.disabled = !checkbox.checked;
                position.tabIndex = tabIndex++;
            }
        }

        gridJsObject.checkboxCheckCallback = registerCustomers;
        gridJsObject.initRowCallback = customersRowInit;
        gridJsObject.rowClickCallback = customerRowClick;


        if (gridJsObject.rows) {
            gridJsObject.rows.each(function (row) {
                customersRowInit(gridJsObject, row);
            });
        }
    };
});
