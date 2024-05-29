/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define(['MageWorx_RewardPoints/js/view/summary/rewardpoints'], function (Component) {
    'use strict';

    return Component.extend({

        /**
         * @override
         */
        isAvailable: function () {
            return this.getRawValue() < 0;
        }
    });
});
