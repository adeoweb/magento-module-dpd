define(['jquery', 'Magento_Ui/js/form/element/date', 'moment'], function($, DateElement, moment) {
    'use strict';

    return DateElement.extend({
        initConfig: function () {
            this._super();

            this.options.minDate = '+1d';
            this.options.beforeShowDay = $.datepicker.noWeekends;
        },

        getInitialValue: function() {
            return moment().add(1, 'days').format(this.outputDateFormat);
        },

        initialize: function() {
            this._super();

            this.overload();
        }
    });
});