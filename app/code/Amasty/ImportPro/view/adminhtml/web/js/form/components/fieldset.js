define([
    'Magento_Ui/js/form/components/fieldset'
], function (FieldSet) {
    return FieldSet.extend({
        defaults: {
            listens: {
                'visible': 'childrenVisibility'
            }
        },
        initElement: function (elem) {
            this._super();
            elem.visible(this.visible());

            return this;
        },
        childrenVisibility: function () {
            var visible = this.visible();
            _.each(this.elems(), function (elem) {
                elem.visible(visible);
            });
        }
    });
});
