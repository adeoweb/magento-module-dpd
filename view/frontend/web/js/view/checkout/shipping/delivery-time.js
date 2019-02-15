define([
    'jquery',
    'ko',
    'underscore',
    'AdeoWeb_Dpd/js/view/checkout/shipping/abstract',
    'Magento_Checkout/js/model/quote',
    'AdeoWeb_Dpd/js/action/fetch-delivery-time-list',
    'AdeoWeb_Dpd/js/dpd-shipping-data'
], function ($, ko, _, Component, quote, fetchDeliveryTimeListAction, dpdShippingData) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'AdeoWeb_Dpd/view/checkout/shipping/delivery-time',
            deliveryTimes: ko.observableArray([]),
            selectedDeliveryTime: ko.observable(null),
            isDeliveryTimesAvailable: ko.observable(false),
            lastCheckedCity: null
        },

        initObservable: function() {
            this._super();

            quote.shippingMethod.subscribe(this.initDeliveryTimes.bind(this));

            this.deliveryTimes.subscribe(this.calculateAvailability.bind(this));

            this.selectedDeliveryTime(dpdShippingData.getSelectedDeliveryTime());
            this.selectedDeliveryTime.subscribe(function(value) {
                if (value !== undefined) {
                    dpdShippingData.setSelectedDeliveryTime(value);
                }
            });
            return this;
        },

        calculateAvailability: function() {
            if (this.selectedMethod() !== 'dpd_classic') {
                this.isDeliveryTimesAvailable(false);
                return false;
            }

            this.isDeliveryTimesAvailable(this.deliveryTimes().length > 0);

            return true;
        },

        initDeliveryTimes: function() {
            let address = quote.shippingAddress();
            let city = address.city;

            if (!this.calculateAvailability() || this.lastCheckedCity === city) {
                return;
            }

            this.deliveryTimes([]);

            if (!city || !city.length) {
                return;
            }

            this.lastCheckedCity = city;

            fetchDeliveryTimeListAction.fetch(city).done(function(response) {
                if (response.length < 1) {
                    return this;
                }

                let result = [];

                _.each(response, function (item) {
                    result.push(item);
                }.bind(this));

                this.deliveryTimes(result);
            }.bind(this));

            return this;
        }
    });
});