define([
    "Magento_Ui/js/grid/columns/column",
], function (Column) {
    return Column.extend({
        defaults: {
            bodyTmpl: 'Amasty_ExportPro/grid/cells/export-log-modal-show',
            modules: {
                generateModal: 'index = export-log-modal',
                exportProcess: 'index = export-log'
            }
        },
        showLogModal: function (exportHistory) {
            this.generateModal().openModal();
            this.exportProcess().setHistory(exportHistory);
            return this;
        },
    });
});
