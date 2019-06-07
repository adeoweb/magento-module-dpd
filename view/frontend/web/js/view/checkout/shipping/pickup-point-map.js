define([
    'jquery',
    'underscore',
    'Magento_Checkout/js/model/quote',
    'mage/translate',
], function($, _, quote) {
    'use strict';

    return {
        markers: [],
        infoWindow: null,
        map: null,
        geocoder: null,
        isMapInitialized: false,
        clickEventObservable: null,
        activeIconImage: null,
        defaultZoom: 7,

        initMap: function(selectedPickupPointObservable) {
            this.map = new google.maps.Map(
                document.getElementById('dpd-pickup-point-map'), {
                    zoom: this.defaultZoom,
                    disableDefaultUI: true,
                });
            this.geocoder = new google.maps.Geocoder();

            this.clickEventObservable = selectedPickupPointObservable;

            this.infoWindow = new google.maps.InfoWindow();

            this.isMapInitialized = true;

            this.initAddressChangeEvent();

            if (quote.shippingAddress().city) {
                this.setCenterByAddress(quote.shippingAddress());
            }
        },

        initAddressChangeEvent: function() {
            quote.shippingAddress.subscribe(function() {
                let shippingAddress = quote.shippingAddress();

                this.setCenterByAddress(shippingAddress);
            }.bind(this));
        },

        setCenter: function(centerCoords, zoom) {
            if (!this.map) {
                return null;
            }

            if (zoom === undefined) {
                zoom = this.defaultZoom;
            }

            this.map.setCenter(
                new google.maps.LatLng(centerCoords.lat, centerCoords.lng),
            );
            this.map.setZoom(zoom);
        },

        setCenterByAddress: function(address) {
            let formattedAddress = address.city + ',' +
                address.region + ',' +
                address.countryId;

            if (address.street.length) {
                formattedAddress = address.street.join(', ') + ', ' + formattedAddress;
            }

            this.geocoder.geocode({
                'address': formattedAddress,
            }, function(results, status) {
                if (status !== 'OK') {
                    return false;
                }

                let zoom = address.street.length ? 13 : 11;

                this.setCenter({
                    lat: results[0].geometry.location.lat(),
                    lng: results[0].geometry.location.lng(),
                }, zoom);
            }.bind(this));
        },

        updateMarkers: function(pickupPoints) {
            this.resetMarkers();

            let self = this;

            _.each(pickupPoints, function(pickupPoint) {
                let markerPosition = new google.maps.LatLng(
                    pickupPoint.latitude,
                    pickupPoint.longitude,
                );

                let marker = new google.maps.Marker({
                    position: markerPosition,
                    map: this.map,
                    pickupPointData: pickupPoint,
                });

                marker.setIcon({
                    url: this.activeIconImage,
                    size: new google.maps.Size(86, 95),
                });

                marker.addListener('click', function() {
                    self.infoWindow.close();

                    self.clickEventObservable(
                        this.pickupPointData.pickup_point_id);

                    self.updateMarker(marker);

                    self.infoWindow.open(self.map, marker);
                });

                this.markers.push(marker);
            }.bind(this));
        },

        updateMarker: function(marker) {
            let markerData = marker.pickupPointData;

            let content = '<h2>' + markerData.company + '</h2>' +
                '<p><b>' + $.mage.__('Address') + '</b><br>' +
                markerData.street + ', ' + markerData.city + ' ' +
                markerData.postcode + '</p>';

            if (markerData.opening_hours.length) {
                content += '<p><b>' + $.mage.__('Opening Hours') + '</b><br>' +
                    '<table>';

                _.each(markerData.opening_hours, function(item) {
                    let weekday = this.getWeekdayByShortcode(item.weekday);

                    content += '<tr>' +
                        '<td>' + weekday + '</td>' +
                        '<td>' + item.openMorning + '-' + item.closeMorning + '</td>' +
                        '<td>' + item.openAfternoon + '-' + item.closeAfternoon + '</td>' +
                        '</tr>'
                }.bind(this));

                content += '</table></p>';
            }

            this.infoWindow.setContent(
                '<div class="dpd-pickup-point-map-marker-container">' +
                content +
                '</div>'
            );
        },

        resetMarkers: function() {
            _.each(this.markers, function(marker) {
                marker.setMap(null);
            });

            this.markers = [];
        },

        getWeekdayByShortcode: function(code) {
            switch (code) {
                case 'MON':
                    return $.mage.__('Monday');
                case 'TUE':
                    return $.mage.__('Tuesday');
                case 'WED':
                    return $.mage.__('Wednesday');
                case 'THU':
                    return $.mage.__('Thursday');
                case 'FRI':
                    return $.mage.__('Friday');
                case 'SAT':
                    return $.mage.__('Saturday');
                case 'SUN':
                    return $.mage.__('Sunday');
                default:
                    return code;
            }
        }
    };
});