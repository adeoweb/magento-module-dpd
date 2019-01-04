define(['jquery', 'Magento_Ui/js/form/element/single-checkbox'], function($, Checkbox) {
    'use strict';

    return Checkbox.extend({
        onCheckedChanged: function (newChecked) {
            if (!newChecked) {
                $('.admin__field[data-index=' + this.targetElementName + ']').show();
            } else {
                $('.admin__field[data-index=' + this.targetElementName + ']').hide();
            }

            return this._super(newChecked);
        }
    });
});