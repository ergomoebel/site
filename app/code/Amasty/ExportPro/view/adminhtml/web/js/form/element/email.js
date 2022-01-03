define([
    'jquery',
    'Magento_Ui/js/form/element/abstract',
    'Amasty_ExportPro/js/form/tag-it'
], function ($, Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            extraClasses: ''
        },

        initialize: function () {
            this._super();

            $.async('#' + this.uid, function (inputElement) {
                $(inputElement).tagit();
                $(inputElement).parent().children('.tagit').addClass(this.extraClasses);
            }.bind(this));

            return this;
        },
    });
});
