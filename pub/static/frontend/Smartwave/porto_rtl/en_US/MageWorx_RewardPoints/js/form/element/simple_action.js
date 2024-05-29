/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'underscore',
    'jquery',
    'uiRegistry',
    'Magento_Ui/js/form/element/select',
    'mage/translate'
], function (_, $, uiRegistry, select, $t) {
    'use strict';

    return select.extend({

        simpleActionFixedValue: 'by_fixed_action',
        simpleActionSpendValueY: 'spend_y_get_x_action',
        simpleActionSpendValueYZ: 'spend_y_more_than_z_get_x_action',
        simpleActionQtyValueY:   'buy_y_get_x_action',
        simpleActionQtyValueYZ: 'buy_y_more_than_z_get_x_action',

        /**
         * Array of field names that depend on the value of
         * this UI component.
         */
        dependentOnActionFieldNames: [
            'mageworx_rewardpoints_rule_form.mageworx_rewardpoints_rule_form.actions.points_step',
            'mageworx_rewardpoints_rule_form.mageworx_rewardpoints_rule_form.actions.point_stage',
        ],

        /**
         * Reference storage for dependent fields. We're caching this
         * because we don't want to query the UI registry so often.
         */
        dependentOnActionFields : [],

        /**
         * Initialize field component, and store a reference to the dependent fields.
         */
        initialize: function() {
            this._super();

            // We're creating a promise that resolves when we're sure that all our dependent
            // UI components have been loaded. We're also binding our callback because
            // we're making use of `this`
            uiRegistry.promise(this.dependentOnActionFieldNames).done(_.bind(function() {

                // Let's store the arguments (the UI Components we queried for) in our object
                this.dependentOnActionFields = arguments;

                this.processDependentFieldNaming(this.initialValue);
                this.processDependentFieldVisibility(this.initialValue);

            }, this));
        },

        onUpdate: function (value) {

            this.processDependentFieldNaming(value);
            this.processDependentFieldVisibility(value);

            return this._super();
        },

        /**
         * Shows or hides dependent fields.
         *
         * @param actionValue
         */
        processDependentFieldVisibility: function (actionValue) {

            if (actionValue === this.simpleActionFixedValue) {

                _.map(this.dependentOnActionFields, function (obj) {
                    obj.hide();
                });
            }

            if (actionValue === this.simpleActionSpendValueY || actionValue === this.simpleActionQtyValueY) {

                _.map(this.dependentOnActionFields, function (obj) {

                    if (obj.index == 'points_step') {
                        obj.show();
                    }

                    if (obj.index == 'point_stage') {
                        obj.hide();
                    }
                });
            }

            if (actionValue === this.simpleActionSpendValueYZ || actionValue === this.simpleActionQtyValueYZ) {

                _.map(this.dependentOnActionFields, function (obj) {
                    obj.show();
                });
            }
        },


        /**
         * Rename dependent fields.
         *
         * @param actionValue
         */
        processDependentFieldNaming: function (actionValue) {

            if (actionValue === this.simpleActionSpendValueY || actionValue === this.simpleActionSpendValueYZ) {

                _.map(this.dependentOnActionFields, function (obj) {

                    if (obj.index == 'points_step') {
                        obj.label = $t('Y spent');
                        obj.notice = $t('It regulates the amount a customer must spend to get "X points". "X points" are assigned each time the customer spends the set amount.');
                    }

                    if (obj.index == 'point_stage') {
                        obj.label = $t('Z spent');
                        obj.notice = $t('It regulates the min amount a customer must spend after which "Y spent" will be applied.');
                    }
                });
            }

            if (actionValue === this.simpleActionQtyValueY || actionValue === this.simpleActionQtyValueYZ) {

                _.map(this.dependentOnActionFields, function (obj) {

                    if (obj.index == 'points_step') {
                        obj.label = $t('Y qty');
                        obj.notice = $t('It regulates the qty a customer must add to receive "X points". "X points" are assigned each time the customer adds the set qty.');
                    }

                    if (obj.index == 'point_stage') {
                        obj.label = $t('Z qty');
                        obj.notice = $t('It regulates the min qty a customer must add after which "Y qty" will be applied.');
                    }
                });
            }
        }
    });
});