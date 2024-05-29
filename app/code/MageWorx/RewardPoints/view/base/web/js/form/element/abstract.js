/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'Magento_Ui/js/form/element/abstract',
    'underscore'
], function (Element, _) {
    'use strict';

    return Element.extend({

        /**
         * Calls initObservable of parent class.
         * Defines observable properties of instance.
         *
         * @returns {Object} Reference to instance
         */
        initObservable: function () {

            this.observe(true, 'notice');
            this._super();
            this.observe(true, 'label');
            return this;
        }
    });
});