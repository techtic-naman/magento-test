/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'Magento_Ui/js/lib/step-wizard'
], function (StepWizard) {
    'use strict';

    return StepWizard.extend({

        /**
         * Select next step.
         */
        next: function () {
            this._super();
            this.wizard.getStep().afterShow();
        },

        /**
         * Select previous step.
         */
        back: function () {
            this._super();
            this.wizard.getStep().afterShow();
        },

        /**
         * @param {Object} data
         * @param {Object} event
         */
        showSpecificStep: function (data, event) {
            this._super(data, event);
            this.wizard.getStep().afterShow();
        }
    });
});
