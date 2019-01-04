define([
    'jquery',
    'ko',
    'underscore',
    'AdeoWeb_Dpd/js/view/checkout/shipping/abstract',
    'Magento_Checkout/js/model/quote',
    'AdeoWeb_Dpd/js/action/fetch-pickup-point-list',
], function($, ko, _, Component, quote, fetchPickupPointListAction) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'AdeoWeb_Dpd/view/checkout/shipping/pickup-point',
            pickupPoints: ko.observableArray([]),
            selectedPickupPoint: ko.observable(null),
        },

        initialize: function() {
            this._super();

            this.initPickupPointsObservable();

            return this;
        },

        initPickupPointsObservable: function() {
            quote.shippingAddress.subscribe(function() {
                this.pickupPoints([]);

                fetchPickupPointListAction.fetchByCountry(quote.shippingAddress().countryId).
                    done(function(response) {
                        if (response.length < 1) {
                            return this;
                        }

                        _.each(response, function(item) {
                            this.pickupPoints.push(item);
                        }.bind(this));
                    }.bind(this));

                return this;
            }.bind(this));
        },
    });
});