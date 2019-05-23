define([
    'jquery',
    'Magento_Ui/js/form/element/date'
], function($, DateElement) {
    'use strict';

    return DateElement.extend({
        initConfig: function () {
            this._super();

            this.options.minDate = '0';

            return this;
        }
    });
});