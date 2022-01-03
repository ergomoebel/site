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
        setHistory: function (importHistory) {
            var messages = importHistory.messages
                ? JSON.parse(importHistory.messages)
                : [];
            this.messages(messages);

            return this;
        },
    });
});
