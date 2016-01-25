var FeedExportAttributeHelper = {
    currentFrom       : {},
    currentTo         : {},
    
    currentPosition   : null,
    currentText       : null,
    currentPattern    : null,
    
    skipPatternToHtml : false,

    init: function()
    {
        var self = this;

        self.editor = window.editors[0];

        setInterval(function() {
            var cursor = self.editor.getCursor();

            self.currentPosition = cursor;

            var text = self.editor.getLine(self.currentPosition.line);

            if (self.currentText != text) {
                self.currentText = text;
                self.currentPattern = self.parsePattern(self.currentText);

                if (!self.skipPatternToHtml) {
                    self.patternToHtml(self.currentPattern);
                } else {
                    self.skipPatternToHtml = false;
                }
            } 
        }, 200);

        Event.observe($('attr_helper_formatters'), 'keyup', function(event) {
            self.htmlToPattern();
        });

        Event.observe($('attr_helper_attribute'), 'change', function(event) {
            self.htmlToPattern();
        });
    },

    parsePattern: function(line)
    {
        var self    = this;
        var pattern = null;

        self.currentFrom = {};
        self.currentTo   = {};
        self.currentFrom.line = self.currentPosition.line;
        self.currentTo.line = self.currentPosition.line;


        var match = line.match(/{([^}]+)(\sparent|\sgrouped|\sconfigurable|\sbundle)?([^}]*)}/);
        if (match != null) {
            var expr = match[0];
            var matches = expr.match(/{([^},]+)(\sparent|\sgrouped|\sconfigurable|\sbundle)?([^}]*)}/);
            var key  = matches[1];
            var type = matches[2];

            

            self.currentFrom.ch = match.index;
            self.currentTo.ch   = self.currentFrom.ch + expr.length;

            var formatters = [];
            if (matches[3] != "") {     
                var expr = /(\[([^\]]+)\])/ig;
                while(res = expr.exec(matches[3]) ) {
                    formatters.push(res[2])
                }
            }

            pattern = {
                'key'        : key,
                'type'       : type,
                'formatters' : formatters
            };
        } else {
            self.currentFrom.ch = self.currentPosition.ch;
            self.currentTo.ch   = self.currentPosition.ch;
        }

        return pattern;
    },

    htmlToPattern: function()
    {
        var self = this;

        self.htmlToPatternFlag = true;
        var htmlFormatters = $('attr_helper_formatters').value;
        var formatters = htmlFormatters.split("\n");

        if (self.currentPattern == null) {
            self.currentPattern = {};
            self.currentPattern.key = 'test';
        }

        self.currentPattern.formatters = formatters;
        self.currentPattern.key = $$('select#attr_helper_attribute option:selected')[0].value;
        $('attr_helper_line').innerHTML = self.patternToLine(self.currentPattern);

        self.skipPatternToHtml = true;
        self.editor.replaceRange($('attr_helper_line').innerHTML, self.currentFrom, self.currentTo);
        self.currentTo.ch = self.currentFrom.ch + $('attr_helper_line').innerHTML.length;
    },

    patternToHtml: function(pattern)
    {
        console.log('patternToHtml');
        if (!pattern) {
            $('attr_helper_line').innerHTML = '';
            $$('textarea#attr_helper_formatters')[0].value = '';
            return;
        }

        var options = $$('select#attr_helper_attribute option');
        var len = options.length;
        for (var i = 0; i < len; i++) {
            if (options[i].value == pattern.key) {
                options[i].selected = true;
            }
        }

        $$('textarea#attr_helper_formatters')[0].value = '';
        for (var i = 0; i < pattern.formatters.length; i++) {
            $$('textarea#attr_helper_formatters')[0].value += pattern.formatters[i];
            $$('textarea#attr_helper_formatters')[0].value += "\n";
        }

        $('attr_helper_line').innerHTML = this.patternToLine(pattern);
    },

    patternToLine: function(pattern)
    {
        var str = '{' + pattern.key;
        if (pattern.formatters.length) {
            for (var i = 0; i < pattern.formatters.length; i++) {
                if (pattern.formatters[i].trim() != '') {
                    str += ', ' + '[' + pattern.formatters[i] + ']';
                }
            }
        }

        str += '}';

        return str;
    }
};