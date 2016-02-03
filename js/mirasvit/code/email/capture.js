if (typeof Prototype !== "undefined") {
    var DataCapture = {
        attachEvents: function ()
        {
            var self = this;

            var inputs = $$('[type=text], [type=email]');
            inputs.each(function(input) {
                input.observe('change', function(item, event) {
                    var e = Event.element(event);
                    self.testValue(item.name, e.value);
                }.bind(this, input));
            });
        },

        testValue: function(name, value)
        {
            if (name == 'billing[firstname]' || name == 'contact[firstname]') {
                this.ajax('firstname', value);
            } else if (name == 'billing[lastname]' || name == 'contact[lastname]') {
                this.ajax('lastname', value);
            } else if (name == 'billing[email]' || name == 'contact[email]') {
                var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                if (re.test(value)) {
                    this.ajax('email', value);
                }
            }
        },

        ajax: function(type, value)
        {
            url = window.location.protocol + '//' + window.location.host + '/index.php/eml/index/capture';

            new Ajax.Request(url, {
                method: 'post',
                parameters: {type: type, value: value}
            });
        }
    };

    document.observe("dom:loaded", function() {
        DataCapture.attachEvents();
    });
} else if (typeof jQuery !== 'undefined') {
    var DataCapture = {
        attachEvents: function ()
        {
            var self = this;

            var inputs = $('[type=text], [type=email]');
            inputs.each(function(key, input) {
                $(input).on('change', function(e) {
                    var $input = $(e.srcElement);
                    self.testValue($input.attr('name'), $input.val());
                });
            });
        },

        testValue: function(name, value)
        {
            if (name == 'billing[firstname]' || name == 'contact[firstname]') {
                this.ajax('firstname', value);
            } else if (name == 'billing[lastname]' || name == 'contact[lastname]') {
                this.ajax('lastname', value);
            } else if (name == 'billing[email]' || name == 'contact[email]') {
                var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                if (re.test(value)) {
                    this.ajax('email', value);
                }
            }
        },

        ajax: function(type, value)
        {
            url = window.location.protocol + '//' + window.location.host + '/index.php/eml/index/capture';

            $.ajax(url, {
                method: 'post',
                data: {type: type, value: value}
            });
        }
    };

    $(document).ready(function() {
        DataCapture.attachEvents();
    });
}