/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'underscore',
    'uiCollection'
    // 'Magento_Ui/js/form/components/group'
], function (_, Group) {
    'use strict';

    return Group.extend({
        defaults: {
            template: 'MageWorx_ReviewAIBase/grid/group'
        }
    });
});
