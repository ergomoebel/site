define([
    'jquery',
    'Magento_Ui/js/form/element/abstract'
], function (jQuery, Abstract) {
    'use strict';

    return Abstract.extend({
        setInitialValue: function () {
            this._super();

            if (!this.isDifferedFromDefault()) {
                this.isUseDefault(true);
            }

            return this;
        }
    });
});
