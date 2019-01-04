define([
    'jquery',
    'AdeoWeb_Dpd/js/modal/dpd-base-modal',
    'underscore',
    'notification'
], function($, Modal, _) {
    'use strict';

    return Modal.extend({
        dataPrefix: 'sales_order_grid.sales_order_grid.dpd_call_courier_modal.call_courier.',
        modalSelector: '.sales_order_grid_sales_order_grid_dpd_call_courier_modal .modal-inner-wrap',
        zones: []
    });
});