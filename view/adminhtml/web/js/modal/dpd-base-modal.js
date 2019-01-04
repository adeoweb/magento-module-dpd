define([
    'jquery',
    'Magento_Ui/js/modal/modal-component',
    'underscore',
    'notification'
], function($, Modal, _) {
    'use strict';

    return Modal.extend({
        dataPrefix: '',
        modalSelector: '',

        initialize: function() {
            this._super();

            $(this).notification();

            return this;
        },

        openModal: function() {
            $(this).notification('clear');

            return this._super();
        },

        submitAjax: function() {
            this.applyData();

            this.valid = true;
            this.elems().forEach(this.validate, this);

            if (this.valid && this.submitUrl) {
                $.ajax({
                    url: this.submitUrl + '?isAjax=true',
                    type: 'POST',
                    showLoader: true,
                    dataType: 'json',
                    data: {request: this.prepareData(), form_key: window.FORM_KEY},
                }).done(function(response) {
                    if (response.error) {
                        this.addErrorMessage(response.message);

                        return;
                    }

                    this.addSuccessMessage(response.message);

                    this.closeModal();

                }.bind(this));
            }
        },

        beforePrepareData: function(data) {
            return data;
        },

        prepareData: function() {
            let data = {};

            data = this.beforePrepareData(data);

            _.each(this.applied, function(value, key) {
                key = key.replace(this.dataPrefix, '');

                let keyTokens = key.split('.');

                if (keyTokens.length > 1 && this.zones.indexOf(keyTokens[0])) {
                    if (data[keyTokens[0]] === undefined) {
                        data[keyTokens[0]] = {};
                    }

                    data[keyTokens[0]][keyTokens[1]] = value;
                    return;
                }

                data[key] = value;
            }.bind(this));

            return data;
        },

        addSuccessMessage: function(responseText) {
            $(this).notification('clear');
            $(this).notification('add', {
                message: responseText,
                messageContainer: '.notices-wrapper'
            });
        },

        addErrorMessage: function(responseText) {
            $(this).notification('clear');
            $(this).notification('add', {
                error: true,
                message: responseText,
                messageContainer: this.modalSelector
            });
        }
    });
});