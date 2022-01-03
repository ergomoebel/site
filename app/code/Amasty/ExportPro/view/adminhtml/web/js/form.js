define([
    'Magento_Ui/js/form/form',
    'underscore',
    'jquery',
    'Amasty_ExportPro/js/action/notification'
], function (Form, _, $, notification) {
    'use strict';

    return Form.extend({
        defaults: {
            listens: {
                'responseData': 'processResponse'
            },
            modules: {
                run: 'index = run'
            }
        },

        processResponse: function () {
            var responseData = this.responseData();

            this.source.remove('data.save_and_run');

            if (!_.isUndefined(responseData.messages)) {
                $.each(responseData.messages, function (key, message) {
                    notification.add(message, responseData.error);
                });
            }

            if (!_.isUndefined(responseData.redirect)) {
                window.location.href = responseData.redirect;
            }

            if (!_.isUndefined(responseData.generate) && responseData.generate) {
                this.run().execute(this.source.data[this.run().idField]);
            }
        },

        saveAndRun: function () {
            this.validate();
            if (!this.source.get('params.invalid')) {
                this.source.set('data.save_and_run', 1);
                this.submit();
            } else {
                this.focusInvalid();
            }
        }
    });
});
