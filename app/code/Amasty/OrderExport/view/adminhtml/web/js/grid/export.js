define([
    'Magento_Ui/js/grid/export',
    'underscore'
], function (Export, _) {
    'use strict';

    return Export.extend({
        defaults: {
            template: 'Amasty_OrderExport/element/export-button',
            modules: {
                runProfile: 'index = run'
            }
        },

        /**
         * Redirect to built option url.
         */
        applyOption: function () {
            var option = this.getActiveOption(),
                url = this.buildOptionUrl(option);

            if (_.has(option, 'is_amasty_profile')) {
                this.runProfile().exportProcess().startUrl = url;
                this.runProfile().execute(option.value);
            } else {
                location.href = url;
            }
        }
    });
});
