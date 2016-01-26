var FeedGenerator = {

    messageTimeout: null,
    isAbort: false,

    generate: function(url, stateUrl, id, status)
    {
        var self      = this;
        this.url      = url;
        this.feedId   = id;
        this.stateUrl = stateUrl;
        this.loader   = new FeedGeneratorLoader(this.stateUrl, this.feedId);

        this.request = new Ajax.Request(this.url, {
            method     : 'POST',
            parameters : {id: this.feedId},
            loaderArea : false,

            onCreate: function() {
                self.loader.start();
            },

            onComplete: function(response) {
                self.loader.finish();

                if (response.responseText.isJSON()) {
                    var json = response.responseText.evalJSON();

                    if (json.success) {
                        if (json.status == 'ready') {
                            self.addMessage('success', json.message);
                        } else if (json.status != 'error') {
                            self.generate(self.continueUrl(url), stateUrl, id, json.status);
                        } else if (json.status == 'error') {
                            self.addMessage('error', json.message);
                        }
                    } else {
                        self.addMessage('error', json.message);
                    }
                } else {
                    if (!self.isAbort) {
                        self.addMessage('error', response.responseText);
                        self.generate(self.continueUrl(url), stateUrl, id, 'processing');
                    }
                }
            }
        });
    },

    addMessage: function(type, text)
    {
        var self = this;

        clearTimeout(self.messageTimeout);
        $('messages').innerHTML = '';
        $('messages').show();

        if (text !== '') {
            $('messages').insert('<ul class="messages"><li class="' + type + '-msg"><ul><li><span>' + text + '</span></li></ul></li></ul>');
            self.messageTimeout = setTimeout(function() {
                $('messages').fade({duration: 3.0, from: 0, to: 1});
            }, 10000);

        }
    },

    continueUrl: function(url)
    {
        url = url.replace('mode/new', 'mode/continue');

        return url;
    },

    abort: function()
    {
        console.log('abort');
        if (this.request) {
            this.isAbort = true;
            this.loader.finish();
            this.request.transport.abort();
            this.request = null;
        }
    },

    generateTest: function(button, url, send)
    {
        if (send) {
            var separator = (url.substr(-1, 1) === '/' ? '' : '/');
            var ids = $('generate_test').value;
            window.open(url + separator + 'ids/' + ids, '_blank','width=800,height=700,resizable=1,scrollbars=1');
        } else {
            $(button).hide();
            var div = document.createElement('div');
            var btn = $('btn_feed_generate_continue');
            if (!btn) {
                btn = $('btn_feed_generate');
            }

            div.style.display = 'inline';
            div.innerHTML = '<input title="Leave empty to generate 100 random products or enter comma-separated product IDs" type="text" id="generate_test" value="" placeholder="Product IDs separated by comma" class="required-entry absolute-advice input-text" style="margin-left: 5px; width:200px">'
                + '<button type="button" class="scalable" onclick="FeedGenerator.generateTest(this,\'' + url + '\', true)"><span><span><span>Submit</span></span></span></button>';
            btn.parentElement.insertBefore(div, btn);

            new Tooltip($('generate_test'), {mouseFollow: true, delay:0});
        }
    }
};