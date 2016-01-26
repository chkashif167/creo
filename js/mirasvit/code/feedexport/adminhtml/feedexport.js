document.observe('dom:loaded', function() {
    if ($('type')) {
        FeedExportMapping.changeFormat($('type'));
    }

    window.editors = [];
    $$('.codemirror').each(function(item, index) {
        var editor = CodeMirror.fromTextArea(item, {
            mode           : {name: 'xml', alignCDATA: true},
            lineNumbers    : true,
            matchTags      : true,
            viewportMargin : Infinity
        });

        setInterval(function() {
            editor.refresh();
            editor.save()
        }, 100);
        window.editors.push(editor);
    });

    var dataElements = $$('input', 'select', 'textarea');
    var buttons      = ['btn_feed_generate', 'btn_feed_generate_new', 'btn_feed_generate_continue', 'btn_feed_delivery'];
    for(var i = 0; i < dataElements.length; i++) {
        if(dataElements[i] && dataElements[i].id){
            Event.observe(dataElements[i], 'change', function(e) {
                for(var i = 0; i < buttons.length; i++) {
                    id = buttons[i];
                    if ($(id)) {
                        $(id).addClassName('disabled');
                    }
                }
            });
        }
    }

});