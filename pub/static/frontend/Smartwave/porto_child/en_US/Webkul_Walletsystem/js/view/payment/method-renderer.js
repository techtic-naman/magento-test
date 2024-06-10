/**
 * Webkul Software
 *
 * @category Webkul
 * @package Webkul_Walletsystem
 * @author Webkul
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list',
        'Magento_Checkout/js/model/cart/totals-processor/default'
    ],
    function (
        Component,
        rendererList,
        defaultTotal
    ) {
        'use strict';
        defaultTotal.estimateTotals();
        rendererList.push(
            {
                type: 'walletsystem',
                component: 'Webkul_Walletsystem/js/view/payment/method-renderer/walletsystem'
            }
        );

        return Component.extend({

        });
    }
);