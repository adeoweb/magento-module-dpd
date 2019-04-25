define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/element/abstract',
    './validator/postcode-validator',
    'mage/translate',
], function($, _, AbstractElement, postcodeValidator, $t) {
    'use strict';

    let timeout = null;

    return AbstractElement.extend({
        initialize: function() {
            this._super();

            this.additionalClasses = _.extend(this.additionalClasses, {
                customnotice: true
            });
        },

        onUpdate: function() {
            this._super();

            clearTimeout(timeout);
            setTimeout(this.validatePostcode.bind(this), 500);
        },

        validatePostcode: function() {
            let countryId = $('select[name="country"]').val(),
                validationResult,
                warnMessage,
                postcodeElement = this;

            if (postcodeElement == null || postcodeElement.value() == null) {
                return true;
            }

            postcodeElement.notice(null);
            validationResult = postcodeValidator.validate(
                postcodeElement.value(), countryId);

            if (!validationResult) {
                warnMessage = $t(
                    'Provided Zip/Postal Code seems to be invalid.');

                if (postcodeValidator.validatedPostCodeExample.length) {
                    warnMessage += $t(' Example: ') +
                        postcodeValidator.validatedPostCodeExample.join('; ') +
                        '. ';
                }
                warnMessage += $t(
                    'If you believe it is the right one you can ignore this notice.');

                postcodeElement.notice(warnMessage);
            }

            return validationResult;
        },
    });
});