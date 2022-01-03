define([
    'jquery',
    'Magento_Ui/js/form/element/file-uploader',
], function ($, FileUploader) {

    return FileUploader.extend({
        defaults: {
            deleteUrl: '',
        },

        initialize: function () {
            this._super();
            this.inputName = 'key_file';

            return this;
        },

        onFileUploaded: function (e, data) {
            if (this.getInitialValue()[0] !== undefined && !data.result.error) {
                this.deleteExistingFile(this.getInitialValue()[0].name);
            }

            this._super(e, data);
        },

        removeFile: function (file) {
            if (this.deleteExistingFile(file.name)) {
                this._super();
            }

            return this;
        },

        deleteExistingFile: function (fileHash) {
            var success = false;
            $.ajax({
                url: this.deleteUrl,
                context: this,
                async: false,
                type: 'GET',
                data: {fileHash: fileHash},
            }).done(function (response) {
                if (!response.error) {
                    success = true;
                } else {
                    this.notifyError(response.error);
                }
            });

            return success;
        }
    });
});
