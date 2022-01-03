define([
    'uiElement',
    'underscore',
    'jquery',
    'ko',
    'mage/translate'
], function (Element, _, $, ko, $t) {
    return Element.extend({
        defaults: {
            steps: [{
                code: 'start',
                action: 'start',
                title: $t('Step 1'),
                template: 'Amasty_ExportPro/run/start'
            }, {
                code: 'process',
                action: 'process', title: $t('Step 2'),
                template: 'Amasty_ExportPro/run/process'
            }, {
                code: 'result',
                title: $t('Step 3'),
                template: 'Amasty_ExportPro/run/result',
                activeStateText: $t('Export Finished')
            }],
            currentTemplate: null,
            indexedSteps: {},
            currentStep: null,
            listens: {
                currentStep: 'stepChanged'
            },
            // custom
            finishedText: $t('Export Finished'),
            startUrl: null,
            statusUrl: null,
            messages: [],
            downloadUrl: null,
            percentage: 0,
            id: 0,
            idField: '',
            processIdentity: ''
        },

        initialize: function () {
            this._super();

            _.each(this.steps, function (val, key) {
                this.indexedSteps[val.code] = key;
            }.bind(this));

            return this;
        },

        initObservable: function () {
            this._super().observe([
                'currentStep',
                'currentTemplate',
                'messages',
                'percentage'
            ]);

            return this;
        },

        nextStep: function () {
            var index = parseInt(this.indexedSteps[this.currentStep()]);

            if (index + 1 < this.steps.length) {
                this.currentStep(this.steps[index + 1].code);
            }
        },

        prevStep: function () {
            var index = parseInt(this.indexedSteps[this.currentStep()]);

            if (index - 1 >= 0) {
                this.currentStep(this.steps[index - 1].code);
            }
        },

        stepChanged: function () {
            var stepConfig = this.steps[this.indexedSteps[this.currentStep()]];

            if (!_.isUndefined(stepConfig.action) && _.isFunction(this[stepConfig.action])) {
                this[stepConfig.action]();
            }
        },

        isDoneStep: function (code) {
            return this.indexedSteps[this.currentStep()] > this.indexedSteps[code];
        },

        // custom
        start: function () {
            var startData = {};
            startData[this.idField] = this[this.idField];
            this.percentage(0);
            this.messages([]);

            $.ajax({
                url: this.startUrl,
                data: startData,
                type: 'POST',
                success: function (result) {
                    if (!_.isUndefined(result.processIdentity)) {
                        this.processIdentity = result.processIdentity;
                        this.nextStep();
                    }
                }.bind(this)
            });
        },

        process: function () {
            this.getStatus().done(function (data) {
                if (data.total > 0) {
                    this.percentage(Math.round(data.proceed / data.total * 100));
                }

                if (data.messages !== undefined) {
                    this.messages(data.messages);
                } else {
                    this.messages([]);
                }

                if (data.status === 'running' || data.status === 'starting') {
                    setTimeout(this.process.bind(this), 1000);
                }

                if (data.status === 'success') {
                    this.nextStep();
                }
            }.bind(this));
        },

        getStatus: function () {
            var result = $.Deferred();

            $.get(this.statusUrl, { 'processIdentity': this.processIdentity }, function (data) {
                result.resolve(data);
            });

            return result;
        },

        getDownloadLink: function () {
            return this.downloadUrl.replace('_process_identity_', this.processIdentity);
        }
    });
});
