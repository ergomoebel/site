define([
    'jquery'
], function ($) {
    'use strict'

    return function (Selector) {
        return Selector.extend({
            defaults: {
                externalProvider: "amimport_import_form.amimport_import_form.file_config.file_source_type",
                listens: {
                    importSourceValue: 'updateFileTypes'
                },
                imports: {
                    importSourceValue: '${ $.externalProvider }:value'
                }
            },

            updateFileTypes: function (value) {
                if (this.index === 'source') {
                    var option = $('#' + this.uid).find('[value="xml"]')[0];

                    if (value === 'google_sheet') {
                        option.disabled = true;
                        this.reset();
                    } else {
                        option.disabled = false;
                    }
                }
            }
        });
    }
});
