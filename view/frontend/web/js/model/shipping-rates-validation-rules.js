define(
    [],
    function () {
        'use strict';
        return {
            getRules: function() {
                return {
                    'city': {
                        'required': true
                    },
                    'postcode': {
                        'required': true
                    },
                    'country_id': {
                        'required': true
                    }
                };
            }
        };
    }
)