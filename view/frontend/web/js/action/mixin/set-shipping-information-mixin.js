define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
    'Magento_Ui/js/model/messageList',
    'mage/translate'
], function($, wrapper, quote, globalMessageList, $t) {
    'use strict';

    return function (shippingInformationAction) {

        return wrapper.wrap(shippingInformationAction, function (originalAction) {
            let selectedShippingMethod = quote.shippingMethod();

            if (selectedShippingMethod.carrier_code === 'dpd' && selectedShippingMethod.method_code === 'pickup') {
                let pickupPoint = $('#dpd-pickup-point-select').val();

                if (!pickupPoint) {
                    globalMessageList.addErrorMessage({ message: $t('Please select a pickup point')});
                    jQuery(window).scrollTop(0);
                    return { done: function (){ } };
                }
            }
            return originalAction();
        });
    };
});