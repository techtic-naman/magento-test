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
     * @param config
     * @param element
     */
    return function (config, element) {
         function init() {
            updateContent();
        }

         function updateContent() {
            $.ajax({
                url: config.ajaxUrl,
                method: 'POST',
                data: {
                    countdownTimerId: config.countdownTimerId
                },
                success: function (result) {
                    processSuccess(result);
                },
            });
        }

        function processSuccess(result) {
            if (result.success) {
                new MwCountdown(
                    result.timeStamp * 1000,
                    element,
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

                show();
            }
        }

        function show() {
            $(element).show();
        }

        function hide() {
            $(element).hide();
        }

        init();
    };
});
