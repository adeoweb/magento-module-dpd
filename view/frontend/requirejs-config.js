let config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/set-shipping-information': {
                'AdeoWeb_Dpd/js/action/mixin/set-shipping-information-mixin': true
            },
            'Magento_Checkout/js/view/shipping-information': {
                'AdeoWeb_Dpd/js/view/mixin/shipping-information-mixin': true
            }
        }
    }
};