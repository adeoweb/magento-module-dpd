define([
    'jquery',
    'AdeoWeb_Dpd/js/modal/dpd-base-modal',
    'underscore'
], function($, Modal, _) {
    'use strict';

    return Modal.extend({
        dataPrefix: 'sales_order_view_shipment_grid.sales_order_view_shipment_grid.dpd_collection_request_modal.',
        modalSelector: '.sales_order_view_shipment_grid_sales_order_view_shipment_grid_dpd_collection_request_modal .modal-inner-wrap',
        zones: [
            'sender_address', 'recipient_address', 'package_info'
        ],

        beforePrepareData: function(data) {
            let orderId = $('input[name=order_id]').val();

            if (orderId) {
                data.order_id = orderId;
            }

            return data;
        }
    });
});