define([
    'jquery',
    'Magento_Checkout/js/model/quote',
    'AdeoWeb_Dpd/js/dpd-shipping-data',
    'AdeoWeb_Dpd/js/view/checkout/shipping/pickup-point'
], function($, quote, dpdShippingData) {
    'use strict';

    let mixin = {
        getShippingMethodTitle: function() {
            if (quote.shippingMethod() &&
                quote.shippingMethod().carrier_code === 'dpd' &&
                quote.shippingMethod().method_code === 'pickup') {
                let additionalData = dpdShippingData.getSelectedPickupPointText();

                if (additionalData) {
                    $('.ship-via .shipping-information-content').
                        append('<br><span class="value">' + additionalData +
                            '</span>');
                }
            }

            return this._super();
        },
    };

    return function(target) {
        return target.extend(mixin);
    };
});
