define([
    'Magento_Ui/js/form/element/select',
    'Amasty_ImportCore/js/storage/typical-fields'
], function (Select, typicalFields) {
    'use strict';

    return Select.extend({
        defaults: {
            listens: {
                value: 'onUpdateHandler'
            },
            modules: {
                dataProvider: '${ $.provider }',
                fields: '${ $.fieldsProvider }'
            }
        },

        onUpdateHandler: function (value) {
            if (value) {
                typicalFields.update(
                    { 'identifier': value }
                );
            }
        }
    });
});
