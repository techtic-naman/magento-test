/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'mage/translate'
], function ($, _, $t) {
    'use strict';

    /**
     * CountdownTimer constructor.
     *
     * @param config
     * @param element
     */
    var CountdownTimer = function (config, element) {
        this.config  = config;
        this.element = element;

        this.init = function () {
            this.updateContent();

            return this;
        };

        this.updateContent = function () {
            var self = this;

            $.ajax({
                url: this.config.ajaxUrl,
                method: 'POST',
                data: {
                    productId: this.config.productId
                },
                success: function (result) {
                    self._processSuccess(result);
                },
            });
        };

        this._processSuccess = function (result) {
            if (result.success) {
                new MwCountdown(
                    result.timeStamp * 1000,
                    this.element,
                    {
                        theme: result.theme,
                        prefix: result.beforeTimerText,
                        suffix: result.afterTimerText,
                        labels: {
                            days:    $t('days'),
                            hours:   $t('hrs'),
                            minutes: $t('min'),
                            seconds: $t('sec')
                        },
                        accent: result.accent
                    }
                );
                this.show();
            }
        };

        this.show = function () {
            $(this.element).show();
        };

        this.hide = function () {
            $(this.element).hide();
        };

        this.init();
    };

    return {
        'countdown-timer': CountdownTimer
    }
});
