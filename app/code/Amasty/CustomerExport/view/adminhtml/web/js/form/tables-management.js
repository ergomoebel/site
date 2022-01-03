define([
    'uiElement',
    'underscore',
    'jquery'
], function (Element, _, $) {
    'use strict';

    return Element.extend({
        defaults: {
            refTableFieldsUrl: '',
            mainTableFieldsUrl: '',
            modules: {
                referencedKeyProvider: '${ $.parentName }.referenced_table_key',
                baseKeyProvider: '${ $.parentName }.base_table_key'
            },
            imports: {
                'table_to_join': '${ $.parentName }.table_to_join:value',
                'parent_entity': '${ $.parentName }.parent_entity:value'
            },
            listens: {
                'table_to_join': 'initReferencedKeyOptions',
                'parent_entity': 'initBaseKeyOptions'
            }
        },
        initReferencedKeyOptions: function () {
            var ajaxData = {table: this.table_to_join};

            this.makeRequest(this.referencedKeyProvider(), this.refTableFieldsUrl, ajaxData);
        },
        initBaseKeyOptions: function () {
            var ajaxData = {table: this.parent_entity};

            this.makeRequest(this.baseKeyProvider(), this.mainTableFieldsUrl, ajaxData);
        },
        makeRequest: function (provider, url, ajaxData) {
            $.ajax({
                url: url,
                data: ajaxData,
                method: 'post',
                global: false,
                dataType: 'json',
                success: function (data) {
                    if (!_.isEmpty(data) && !data.error) {
                        provider.visible(true);
                        provider.options(data);
                        provider.cacheOptions.plain = JSON.parse(JSON.stringify(data));
                        provider.cacheOptions.tree = JSON.parse(JSON.stringify(data));
                        if (!_.isEmpty(provider.value())) {
                            let clear = true;
                            _.each(data, function (el) {
                                if (provider.value() === el.value)  {
                                    clear = false;
                                    return;
                                }
                            })
                            provider.value(clear ? '' : provider.value());
                            provider.filterInputValue(clear ? '' : provider.filterInputValue());
                        }
                    }
                    if (data.error) {
                        provider.visible(false);
                    }
                }
            });
        }
    });
});
