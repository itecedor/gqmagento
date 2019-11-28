define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/totals',
        'Magento_Catalog/js/price-utils'
    ],
    function (Component, quote, totals) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'POSIMWebExt_GCLink/gclinkdisplay'
            },
            isIncludedInSubtotal: window.checkoutConfig.isIncludedInSubtotal,
            totals: totals.totals,

            /**
             * @returns {Number}
             */
            getGclink: function () {
                var giftcard = totals.getSegment('posimgiftcard') || totals.getSegment('posimgc_num');

                if (giftcard !== null && giftcard.hasOwnProperty('value')) {
                    return giftcard.value;
                }

                return 0;
            },

            /**
             * Get giftcard value
             * @returns {String}
             */
            getValue: function () {
                return this.getFormattedPrice(this.getGclink());
            },

            /**
             * giftcard display flag
             * @returns {Boolean}
             */
            isDisplayed: function () {
                return this.isFullMode() && this.getGclink() > 0;
            }
        });
    }
);
