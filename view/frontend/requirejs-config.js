var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/set-shipping-information': {
                'AdeoWeb_Dpd/js/action/mixin/set-shipping-information-mixin': true
            },
            'Magento_Checkout/js/model/shipping-save-processor/payload-extender': {
                'AdeoWeb_Dpd/js/model/mixin/shipping-save-processor/payload-extender-mixin': true
            }
        }
    }
};