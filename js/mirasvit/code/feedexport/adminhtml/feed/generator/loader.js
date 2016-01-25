FeedGeneratorLoader = Class.create({
    stateUrl : null,
    loader   : null,
    feedId   : null,
    request  : null,
    started  : null,
    prevText : '',

    initialize: function(stateUrl, feedId)
    {
        this.stateUrl = stateUrl;
        this.feedId   = feedId;
        this.loader   = $('feed_generator_loader');
    },

    start: function()
    {
        this.finish();

        this.started = true;
        this.loader.show();
        this.listen();
    },

    finish: function()
    {
        this.started = false;
        this.loader.hide();

        if (this.request) {
            this.request.transport.abort();
            this.request = null;
        }
    },

    listen: function()
    {
        var self = this;

        self.request = new Ajax.Request(self.stateUrl, {
            method     : 'POST',
            parameters : {id: self.feedId},
            loaderArea : false,
            onSuccess : function(transport) {
                if (transport.status != 200 && !self.started) {
                    return;
                }

                self.updateText(transport.responseText);

                if (self.started) {
                    setTimeout(function() {
                        self.listen();
                    }, 0);
                }
            }
        });
    },

    updateText: function(text)
    {
        if (text !== '') {
            if (this.prevText != text) {
                this.loader.replace(text);
                this.prevText = text;
            }
        }

        this.loader = $('feed_generator_loader');

        if (this.started) {
            this.loader.show();
        } else {
            this.loader.hide();
        }
    }
});