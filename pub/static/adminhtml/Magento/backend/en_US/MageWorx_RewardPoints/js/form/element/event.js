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

        actionValueForShowing: 'order_placed_earn',

        /**
         * Array of field names that depend on the value of
         * this UI component.
         */
        dependentOnActionFieldNames: [
            'mageworx_rewardpoints_rule_form.mageworx_rewardpoints_rule_form.actions.points_step',
            'mageworx_rewardpoints_rule_form.mageworx_rewardpoints_rule_form.actions.point_stage',
            'mageworx_rewardpoints_rule_form.mageworx_rewardpoints_rule_form.actions.calculation_type',
            'mageworx_rewardpoints_rule_form.mageworx_rewardpoints_rule_form.actions.simple_action'
        ],

        actionsFieldsetName: 'mageworx_rewardpoints_rule_form.mageworx_rewardpoints_rule_form.actions',
        conditionsFieldsetName: 'mageworx_rewardpoints_rule_form.mageworx_rewardpoints_rule_form.conditions',

        /**
         * Reference storage for dependent fields. We're caching this
         * because we don't want to query the UI registry so often.
         */
        dependentOnActionFields: [],

        /**
         * Initialize field component, and store a reference to the dependent fields.
         */
        initialize: function () {
            this._super();

            var source = uiRegistry.get(this.provider);

            if (source.data.rule_id) {
                this.disable();
            }

            // We're creating a promise that resolves when we're sure that all our dependent
            // UI components have been loaded. We're also binding our callback because
            // we're making use of `this`
            uiRegistry.promise(this.dependentOnActionFieldNames).done(_.bind(function () {

                // Let's store the arguments (the UI Components we queried for) in our object
                this.dependentOnActionFields = arguments;

                // Set the initial visibility of our fields.
                this.processDependentFieldVisibility(this.initialValue);
            }, this));

            uiRegistry.promise(this.actionsFieldsetName + '.actions_apply_to.html_content').done(_.bind(function () {
                    this.processDependentActionTabVisibility(this.initialValue);
                },
                this
                )
            );

            uiRegistry.promise(this.conditionsFieldsetName).done(_.bind(function () {
                    this.processDependentConditionTabVisibility(this.initialValue);
                },
                this
                )
            );
        },

        onUpdate: function (value) {

            this.processDependentFieldVisibility(value);
            this.processDependentConditionTabVisibility(value);
            this.processDependentActionTabVisibility(value);

            return this._super();
        },

        /**
         * Shows or hides dependent fields.
         *
         * @param actionValue
         */
        processDependentFieldVisibility: function (actionValue) {

            var method = 'hide';

            if (actionValue === this.actionValueForShowing) {
                method = 'show';
            }

            _.map(this.dependentOnActionFields, function (obj) {

                if (actionValue === 'order_placed_earn') {
                    obj.show();

                    if (obj.index == 'simple_action') {

                        var value = obj.value();
                        obj.value('');
                        obj.value(value);
                    }

                } else {
                    obj.hide();
                }
            });
        },


        /**
         * Enable/disable Conditions tab
         */
        processDependentConditionTabVisibility: function (actionValue) {
            var conditions = uiRegistry.get(this.conditionsFieldsetName);

            if (!_.isUndefined(conditions)) {

                if (actionValue === this.actionValueForShowing) {
                    conditions.visible(true);
                } else {
                    conditions.visible(false);
                }
            }
        },

        /**
         * Enable/disable Action Condition tab
         */
        processDependentActionTabVisibility: function (actionValue) {
            var conditionsHtmlContent = uiRegistry.get(this.actionsFieldsetName + '.actions_apply_to.html_content');

            if (actionValue === this.actionValueForShowing) {
                conditionsHtmlContent.visible(true);
            } else {
                conditionsHtmlContent.visible(false);
            }
        }
    });
});
