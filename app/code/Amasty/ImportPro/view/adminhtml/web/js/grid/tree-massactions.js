/**
 * Extending massactions component for providing
 * history listings params in mass actions
 */
define([
    'Magento_Ui/js/grid/tree-massactions',
], function (Massactions) {
    'use strict';

    return Massactions.extend({
        getSelections: function () {
            var selections = this._super(),
                source = this.selections().source();

            if (!source) {
                return selections;
            }
            selections.params.job_type = source.params.job_type;
            selections.params.job_id = source.params.job_id;

            return selections;
        }
    });
});
