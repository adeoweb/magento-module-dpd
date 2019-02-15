define([
    'jquery',
    'ko',
    'underscore',
    'AdeoWeb_Dpd/js/view/checkout/shipping/abstract',
    'Magento_Checkout/js/model/quote',
    'AdeoWeb_Dpd/js/action/fetch-pickup-point-list',
    'AdeoWeb_Dpd/js/dpd-shipping-data',
], function($, ko, _, Component, quote, fetchPickupPointListAction,
            dpdShippingData) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'AdeoWeb_Dpd/view/checkout/shipping/pickup-point',
            pickupPoints: ko.observableArray([]),
            selectedPickupPoint: ko.observable(null),
            lastCountry: null,
        },

        initialize: function() {
            this._super();

            this.initPickupPointsObservable();

            this.selectedPickupPoint(dpdShippingData.getSelectedPickupPoint());
            this.selectedPickupPoint.subscribe(function(value) {
                if (value !== undefined) {
                    dpdShippingData.setSelectedPickupPoint(value);
                }
            });

            return this;
        },

        initPickupPointsObservable: function() {
            quote.shippingAddress.subscribe(function() {
                if (!this.evaluateUpdates(quote.shippingAddress())) {
                    return;
                }

                let result = [];

                this.lastCountry = quote.shippingAddress().countryId;

                fetchPickupPointListAction.fetchByAddress(
                    quote.shippingAddress()).
                    done(function(response) {
                        this.pickupPoints([]);

                        if (response.length < 1) {
                            return this;
                        }

                        _.each(response, function(item) {
                            if (result[item.city] === undefined) {
                                result[item.city] = [];
                                result[item.city]['name'] = item.city;
                                result[item.city]['items'] = [];
                            }

                            item.label = this.formatPickupPointLabel(item);
                            result[item.city]['items'].push(item);
                        }.bind(this));

                        this.pickupPoints(_.sortBy(_.values(result), 'name'));
                    }.bind(this));
                return this;
            }.bind(this));
        },

        evaluateUpdates: function(address) {
            return !(address.countryId === this.lastCountry);
        },

        formatPickupPointLabel: function(item) {
            return item.company + ', ' + item.street;
        }
    });
});