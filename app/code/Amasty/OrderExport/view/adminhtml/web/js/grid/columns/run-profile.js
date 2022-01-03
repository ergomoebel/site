define([
    "Magento_Ui/js/grid/columns/column",
], function (Column) {
    return Column.extend({
        defaults: {
            bodyTmpl: 'Amasty_OrderExport/grid/cells/run-profile',
            modules: {
                runProfile: 'index = run'
            }
        },
        showLogModal: function (profileData) {
            this.runProfile().execute(profileData.id);
        },
    });
});
