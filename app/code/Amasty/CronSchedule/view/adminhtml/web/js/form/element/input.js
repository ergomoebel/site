define([
    'Magento_Ui/js/form/element/abstract',
    'uiRegistry'
], function (Abstract, registry) {
    'use strict';

    return Abstract.extend({
        onUpdate: function () {
            const customValue = '         ';

            if (this.focused()) {
                registry.get('index = frequency').value(customValue);
                this._super();
            }
        }
    });
});
