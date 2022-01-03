define([
    'Magento_Ui/js/form/element/single-checkbox'
], function (Checkbox) {
    'use strict';

    return Checkbox.extend({
        defaults: {
            tmpValue: ''
        },

        hide: function () {
            this.tmpValue = this.value();
            this.source.remove(this.dataScope);

            return this._super();
        },

        show: function () {
            if (this.tmpValue !== '') {
                this.value(this.tmpValue);
            }

            return this._super();
        }
    });
});
