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

                return method != null ? method.carrier_code + '_' +
                    method.method_code : null;
            }, this);

            return this;
        }
    });
});