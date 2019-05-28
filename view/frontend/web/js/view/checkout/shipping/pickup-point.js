define([
    'jquery',
    'ko',
    'underscore',
    'AdeoWeb_Dpd/js/view/checkout/shipping/abstract',
    'Magento_Checkout/js/model/quote',
    'AdeoWeb_Dpd/js/action/fetch-pickup-point-list',
    'AdeoWeb_Dpd/js/dpd-shipping-data',
    './pickup-point-map',
], function(
    $,
    ko,
    _,
    Component,
    quote,
    fetchPickupPointListAction,
    dpdShippingData,
    pickupPointMap,
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'AdeoWeb_Dpd/view/checkout/shipping/pickup-point',
            pickupPoints: ko.observableArray([]),
            selectedPickupPoint: ko.observable(null),
            stateLoading: ko.observable(false),
            stateEmpty: ko.observable(false),
            lastCountry: null,
            countryCenters: [],
            activeIconImage: null,
        },

        rawPickupPoints: [],

        initialize: function() {
            this._super();

            this.initPickupPointsObservable();

            this.selectedPickupPoint(dpdShippingData.getSelectedPickupPoint());
            this.selectedPickupPoint.subscribe(function(value) {
                if (value !== undefined) {
                    dpdShippingData.setSelectedPickupPoint(value);
                }
            }.bind(this));

            return this;
        },

        initMap: function() {
            pickupPointMap.activeIconImage = this.activeIconImage;

            pickupPointMap.initMap(this.selectedPickupPoint);

            this.updateMapCenter();

            if (this.rawPickupPoints.length > 0) {
                pickupPointMap.updateMarkers(this.rawPickupPoints);
            }
        },

        initPickupPointsObservable: function() {
            quote.shippingAddress.subscribe(function() {
                if (!this.evaluateUpdates(quote.shippingAddress())) {
                    return;
                }

                let result = [];

                this.lastCountry = quote.shippingAddress().countryId;
                this.stateLoading(true);

                dpdShippingData.setSelectedPickupPoint(0);

                fetchPickupPointListAction.fetchByAddress(
                    quote.shippingAddress()).
                    done(function(response) {
                        this.stateLoading(false);
                        this.pickupPoints([]);

                        if (response.length < 1) {
                            this.stateEmpty(true);
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

                        this.stateEmpty(false);
                        this.pickupPoints(_.sortBy(_.values(result), 'name'));
                        this.rawPickupPoints = response;

                        if (pickupPointMap.isMapInitialized) {
                            pickupPointMap.updateMarkers(response);
                            this.updateMapCenter();
                        }
                    }.bind(this));
                return this;
            }.bind(this));
        },

        updateMapCenter: function() {
            if (!pickupPointMap.isMapInitialized) {
                return null;
            }

            let countryCenter = {lat: 54.5260, lng: 15.2551};

            if (this.lastCountry && this.countryCenters[this.lastCountry]) {
                countryCenter = this.countryCenters[this.lastCountry];
            }

            pickupPointMap.setCenter(countryCenter);
        },

        evaluateUpdates: function(address) {
            return !(address.countryId === this.lastCountry);
        },

        formatPickupPointLabel: function(item) {
            return item.company + ', ' + item.street + ', ' + item.city + ' ' + item.postcode;
        },

        getSelectedPickupPointText: function () {
            return ko.computed(function() {
                let pickupPointId = this.selectedPickupPoint();

                let pickupPoint = _.find(this.rawPickupPoints, function(item) {
                    if (item.pickup_point_id === pickupPointId.toString()) {
                        return item;
                    }
                }.bind(this));

                if (!pickupPoint) {
                    return false;
                }

                return this.formatPickupPointLabel(pickupPoint);
            }.bind(this));
        }
    });
});