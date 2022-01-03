define([
    'uiElement',
], function (Element) {
    return Element.extend({
        defaults: {
            messages: [],
        },
        initObservable: function () {
            this._super().observe(['messages']);

            return this;
        },
        setHistory: function (exportHistory) {
            var messages = exportHistory.messages
                ? JSON.parse(exportHistory.messages)
                : [];
            this.messages(messages);

            return this;
        },
    });
});
