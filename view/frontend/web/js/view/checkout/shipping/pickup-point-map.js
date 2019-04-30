define([
    'jquery',
    'underscore'
], function($, _) {
    'use strict';

    return {
        markers: [],
        infoWindow: null,
        map: null,
        isMapInitialized: false,
        clickEventObservable: null,
        activeIconImage: null,

        initMap: function(selectedPickupPointObservable) {
            this.map = new google.maps.Map(
                document.getElementById('dpd-pickup-point-map'), {
                    zoom: 7,
                    disableDefaultUI: true
                });

            this.clickEventObservable = selectedPickupPointObservable;

            this.infoWindow = new google.maps.InfoWindow();

            this.isMapInitialized = true;
        },

        setCenter: function(centerCoords) {
            if (!this.map) {
                return null;
            }

            this.map.setCenter(new google.maps.LatLng(centerCoords.lat, centerCoords.lng));
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

                marker.addListener('click', function() {
                    self.infoWindow.close();

                    self.clickEventObservable(this.pickupPointData.pickup_point_id);

                    self.updateMarker(marker);

                    self.infoWindow.open(self.map, marker);
                });

                this.markers.push(marker);
            }.bind(this));
        },

        updateMarker: function(marker) {
            this.resetMarkerIcons();

            let markerData = marker.pickupPointData;

            this.infoWindow.setContent(
                '<b>' + markerData.company + '</b><br>' +
                markerData.street + ', ' + markerData.city + ' ' + markerData.postcode
            );

            marker.setIcon({
                url: this.activeIconImage,
                size: new google.maps.Size(50, 50),
            });
        },

        resetMarkers: function() {
            _.each(this.markers, function(marker) {
                marker.setMap(null);
            });

            this.markers = [];
        },

        resetMarkerIcons: function() {
            _.each(this.markers, function(marker) {
                marker.setIcon(null);
            });
        }
    };
});