define([
    'uiComponent'
], function (Component) {
    return Component.extend({
        defaults: {
            idField: 'id',
            requestIdField: null,
            modules: {
                generateModal: '${ $.name }.modal',
                importProcess: '${ $.name }.modal.import-process'
            }
        },
        execute: function (id, strategy) {
            if (!this.requestIdField) {
                this.requestIdField = this.idField;
            }
            this.importProcess().idField = this.requestIdField;
            this.importProcess()[this.requestIdField] = id;
            this.importProcess().strategy = strategy;
            this.generateModal().openModal();
            this.importProcess().currentStep('start');
        },
        validate: function () {
            return {
                valid: true
            };
        }
    });
})
