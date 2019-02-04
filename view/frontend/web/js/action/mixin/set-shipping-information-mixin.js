define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
    'Magento_Ui/js/model/messageList',
    'mage/translate',
], function($, wrapper, quote, globalMessageList, $t) {
    'use strict';

    return function(shippingInformationAction) {

        return wrapper.wrap(shippingInformationAction,
            function(originalAction) {
                let selectedShippingMethod = quote.shippingMethod();
                let shippingAddress = quote.shippingAddress();

                if (selectedShippingMethod.carrier_code !== 'dpd') {
                    return originalAction();
                }

                let pickupPoint = $('#dpd-pickup-point-select').val();
                let deliveryTime = $('#dpd-delivery-time-select').val();

                if (selectedShippingMethod.method_code === 'pickup' &&
                    !pickupPoint) {
                    globalMessageList.addErrorMessage(
                        {message: $t('Please select a pickup point')});
                    jQuery(window).scrollTop(0);
                    return {done: function() { }};
                }

                if (shippingAddress['extension_attributes'] === undefined) {
                    shippingAddress['extension_attributes'] = {};
                }

                shippingAddress['extension_attributes']['dpd_delivery_options'] = {
                    pickup_point_id: pickupPoint,
                    delivery_time: deliveryTime,
                };

                return originalAction();
            });
    };
});