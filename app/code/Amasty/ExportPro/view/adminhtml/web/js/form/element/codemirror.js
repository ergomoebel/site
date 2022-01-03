define([
    'Magento_Ui/js/form/element/textarea',
    'jquery',
    'Amasty_ExportPro/js/lib/codemirror',
    'Amasty_ExportPro/js/lib/codemirror/twig'
], function (Textarea, $, CodeMirror) {
    'use strict';

    return Textarea.extend({
        editor:null,

        initialize: function () {
            this._super();
            $.async('#' + this.uid, function (elem) {
                this.editor = CodeMirror.fromTextArea(elem, {
                    lineNumbers: true,
                    mode: {name: "twig", base: "text/html"}
                });
                this.editor.on('change', function () {
                    this.value(this.editor.getValue());
                }.bind(this));
            }.bind(this));

            return this;
        },
        setCodeMirrorValue: function (value) {
            this.value(value);
            this.editor.setValue(value);
        }
    });
});
