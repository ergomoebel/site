define([
    'Magento_Ui/js/form/element/single-checkbox'
], function (Checkbox) {
    'use strict';

    return Checkbox.extend({
        defaults: {
            selectValue: '',
            listens: {
                selectValue: 'onSelectChanged'
            }
        },

        initialize: function () {
            this._super();

            if (!this.selectValue) {
                this.hide();
            }

            return this;
        },

        onSelectChanged: function (value) {
            if (value) {
                this.show();
            } else {
                this.hide();
            }
        }
    });
});
