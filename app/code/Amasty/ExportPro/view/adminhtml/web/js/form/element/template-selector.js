define([
    'Magento_Ui/js/form/element/select',
    'underscore'
], function (Select, _) {
    'use strict';

    return Select.extend({
        defaults: {
            twigTemplates: [],
            listens: {
                'update': 'changeTemplate'
            },
            modules: {
                twigHeader: 'index = twig.header',
                twigFooter: 'index = twig.footer',
                twigContent: 'index = twig.content',
                twigExtension: 'index = twig.extension',
                twigSeparator: 'index = twig.separator',
            }
        },
        changeTemplate: function () {
            var selectedTemplate;

            if (_.isUndefined(this.twigTemplates[this.value()])) {
                return;
            }

            selectedTemplate = this.twigTemplates[this.value()];
            this.twigHeader().value(selectedTemplate.header);
            this.twigFooter().value(selectedTemplate.footer);
            this.twigSeparator().value(selectedTemplate.separator);
            this.twigExtension().value(selectedTemplate.extension);
            this.twigContent().setCodeMirrorValue(selectedTemplate.content);
        }
    });
});
