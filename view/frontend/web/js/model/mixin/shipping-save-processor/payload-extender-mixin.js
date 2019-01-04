define([
    'jquery',
    'mage/utils/wrapper',
], function($, wrapper) {
    'use strict';

    return function(payloadExtenderModel) {
        return wrapper.wrap(
            payloadExtenderModel,
            function(originalModel, payload) {
                payload = originalModel(payload);

                let pickupPoint = $('#dpd-pickup-point-select').val();
                let deliveryTime = $('#dpd-delivery-time-select').val();

                payload.addressInformation['extension_attributes']['dpd_delivery_options'] = {
                    pickup_point_id: pickupPoint,
                    delivery_time: deliveryTime
                };

                return payload;
            },
        );
    };
});