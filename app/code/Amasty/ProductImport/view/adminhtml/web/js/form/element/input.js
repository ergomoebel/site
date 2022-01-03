define([
    'Magento_Ui/js/form/element/abstract'
], function (Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            listens: {
                visible: 'onUpdateVisible'
            },
            imports: {
                visible: '${ "index = use_custom_prefix" }:checked'
            }
        },

        onUpdateVisible: function (isVisible) {
            if (isVisible === false) {
                this.value('');
            }
        }
    });
});
