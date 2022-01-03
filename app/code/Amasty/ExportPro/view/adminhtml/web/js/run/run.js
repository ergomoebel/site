define([
    'uiComponent'
], function (Component) {
    return Component.extend({
        defaults: {
            idField: 'id',
            requestIdField: null,
            modules: {
                generateModal: '${ $.name }.modal',
                exportProcess: '${ $.name }.modal.export-process'
            }
        },
        execute: function (id) {
            if (!this.requestIdField) {
                this.requestIdField = this.idField;
            }
            this.exportProcess().idField = this.requestIdField;
            this.exportProcess()[this.requestIdField] = id;
            this.generateModal().openModal();
            this.exportProcess().currentStep('start');
        },
        validate: function () {
            return {
                valid: true
            };
        }
    });
})