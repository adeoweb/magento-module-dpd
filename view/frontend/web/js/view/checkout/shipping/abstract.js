define([
    'jquery',
    'ko',
    'underscore',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
], function ($, ko, _, Component, quote) {
    'use strict';

    return Component.extend({
        initObservable: function () {
            this.selectedMethod = ko.computed(function() {
                let method = quote.shippingMethod();

                if (method != null && method.method_code == 'pickup') {
                    $('#additional_data_classic_dpd').hide();
                    $('#additional_data_pickup_dpd').show();
                } else {
                    $('#additional_data_classic_dpd').show();
                    $('#additional_data_pickup_dpd').hide();
                }

                return method != null ? method.carrier_code + '_' +
                    method.method_code : null;
            }, this);

            return this;
        }
    });
});
