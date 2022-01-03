define([
    "Magento_Ui/js/form/components/insert-listing"
], function (InsertListing) {
    'use strict';

    return InsertListing.extend({
        validate: function () {
            return {
                valid: true,
                target: this
            };
        },
        initialize: function () {
            this._super();
            if (_.isUndefined(this.jobId)) {
                this.visible(false);
            }

            return this;
        }
    });
})