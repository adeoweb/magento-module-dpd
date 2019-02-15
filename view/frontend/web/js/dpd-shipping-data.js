define([
    'jquery',
    'Magento_Customer/js/customer-data',
], function($, storage) {
    'use strict';

    let getEmptyObject = function() {
        return {
            'selectedPickupPoint': null,
            'selectedDeliveryTime': null,
        };
    };

    let cacheKey = 'dpd-shipping-data',

        saveData = function(data) {
            storage.set(cacheKey, data);
        },

        getData = function() {
            let data = storage.get(cacheKey)();

            if ($.isEmptyObject(data)) {
                data = getEmptyObject();
                saveData(data);
            }

            return data;
        };

    return {
        setSelectedPickupPoint: function(data) {
            let obj = getEmptyObject();

            obj.selectedPickupPoint = data;

            saveData(obj);
        },

        getSelectedPickupPoint: function() {
            return getData().selectedPickupPoint;
        },

        setSelectedDeliveryTime: function(data) {
            let obj = getEmptyObject();

            obj.selectedDeliveryTime = data;

            saveData(obj);
        },

        getSelectedDeliveryTime: function() {
            return getData().selectedDeliveryTime;
        },
    };
});
