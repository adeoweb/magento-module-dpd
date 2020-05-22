define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
    'Magento_Ui/js/model/messageList',
    'mage/translate',
    'AdeoWeb_Dpd/js/dpd-shipping-data'
], function($, wrapper, quote, globalMessageList, $t, dpdShippingData) {
    'use strict';

    return function(shippingInformationAction) {

        return wrapper.wrap(
            shippingInformationAction,
            function(originalAction) {
                let selectedShippingMethod = quote.shippingMethod();
                let shippingAddress = quote.shippingAddress();

                if (selectedShippingMethod.carrier_code !== 'dpd') {
                    return originalAction();
                }

                let pickupPoint = selectedShippingMethod.method_code === 'pickup'
                    ? dpdShippingData.getSelectedPickupPoint() : null;
                let deliveryTime = dpdShippingData.getSelectedDeliveryTime();

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
                    api_id: pickupPoint,
                    delivery_time: deliveryTime,
                };

                if (shippingAddress.extensionAttributes === undefined) {
                    shippingAddress.extensionAttributes = {};
                }

                shippingAddress.extensionAttributes.dpdDeliveryOptions = {
                    api_id: pickupPoint,
                    delivery_time: deliveryTime,
                };

                return originalAction();
            });
    };
});