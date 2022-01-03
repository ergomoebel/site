define([
        'Magento_Ui/js/form/element/select',
        'uiRegistry'
    ], function (Select, registry) {
        'use strict';

        return Select.extend({
            defaults: {
                cronFields: ['minute', 'hour', 'day', 'month', 'day_of_week']
            },

            onUpdate: function () {
                const customValue = '         ';

                var fieldValue;

                if (this.value() && this.value() !== customValue) {
                    fieldValue = this.value().split(' ');

                    _.each(
                        this.cronFields,
                        function (value, key) {
                            registry.get('index = ' + value).value(fieldValue[key]);
                        }
                    );

                    this._super();
                }
            }
        });
    }
);
