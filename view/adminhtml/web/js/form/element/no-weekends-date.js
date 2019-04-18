define(['jquery', 'Magento_Ui/js/form/element/date'], function($, Date) {
    'use strict';

    return Date.extend({
        initConfig: function () {
            this._super();

            this.options.beforeShowDay = $.datepicker.noWeekends;
        }
    });
});