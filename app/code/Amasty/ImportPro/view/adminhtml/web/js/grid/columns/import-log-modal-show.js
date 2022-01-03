define([
    "Magento_Ui/js/grid/columns/column",
], function (Column) {
    return Column.extend({
        defaults: {
            bodyTmpl: 'Amasty_ImportPro/grid/cells/import-log-modal-show',
            modules: {
                generateModal: 'index = import-log-modal',
                importProcess: 'index = import-log'
            }
        },
        showLogModal: function (importHistory) {
            this.generateModal().openModal();
            this.importProcess().setHistory(importHistory);
            return this;
        },
    });
});
