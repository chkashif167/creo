document.observe("dom:loaded", function() {
    RegularHighlighter.addArea();
    document.observe('keyup', function(){
        RegularHighlighter.init();
    });
    document.observe('click', function(){
        RegularHighlighter.init();
    });
});

var RegularHighlighter = {

    init : function()
    {
        var slf = this;
        $$("input.regular_expression").each(function (el) {
            var parentId = slf.getParentId(el); // Получим ID родительского эллемента
            var elem = $$("pre#regex" + parentId); // Поле с подсветкой
            var errorArea = $$("div#regex_err" + parentId); // Поле для ошибки
            // Создаем поле для текста с подсветкой
            if (elem.length == 0) {
                elem = slf.addItem(el, parentId);
            }
            // Если надо увеличить ширину input то расширяем
            slf.changeWidthArea(el);
            // Если это регулярное выражение то будем его подсвечивать
            if (el.getValue().search('^[\/].+[\/](i|m|s|x)?$') == 0) {

                    elem[0].update(el.getValue()); // Сразу же перенесем в это поле тексты с input
                    var log = RegexColorizer.colorizeAll('regex' + parentId); // Включаем подсветку для поля

                    // Удаляем поле с сообщением об ошибке
                    slf.removeArea(errorArea);

                    // Если ошибка есть то создаем поле и пишем сообщение
                    if (log.errorLog !== true) {
                        el.up(0).insert('<div class="regex_err" id="regex_err' + parentId + '">' + log.errorLog + '</div>');
                    }
            // Если текст в input не регулярное выражение то удаляем поле для сообщений об ошибке
            } else {
                elem[0].update(el.getValue());
                slf.removeArea(errorArea);
            }
        });
    },

    addArea : function()
    {
        var slf = this;
        $$("input.regular_expression").each(function (el) {
            var parentId = slf.getParentId(el);
            slf.addItem(el, parentId);
        });
        slf.init();
    },

    getParentId : function(el)
    {
        // Вытаскиваем ID родительского эллемента
        var parentId = el.up(0).readAttribute('id');
        // Если у него нет ID то создадим ему его.
        if(parentId == null) {
            var f = Math.floor(Math.random() * (1000000000 - 1000000 + 1)) + 1000000;
            var s = Math.floor(Math.random() * (10000000 - 10000 + 1)) + 10000;
            var parentId = '_' + f + '_' + s;
            el.up(0).writeAttribute('id', parentId);
        }
        return parentId;
    },

    removeArea : function(el)
    {
        if (el.length > 0) {
            el[0].remove();
        }
    },

    changeWidthArea : function(el)
    {
        var parentId = this.getParentId(el);
        var elem = $$("pre#regex" + parentId);
        var fieldLength = el.getValue().length;
        if (fieldLength > 20) {
            el.setStyle({width: (fieldLength * 8) + 'px'});
            el.up().setStyle({minWidth: (fieldLength * 8 + 15) + 'px'});
            elem[0].up().setStyle({width: (fieldLength * 8 + 5) + 'px'});
        } else {
            el.setStyle({width: '160px'});
            el.up().setStyle({minWidth: '165px'});
            elem[0].up().setStyle({width: '165px'});
        }
    },

    addItem : function(el, parentId)
    {
        el.writeAttribute('autocomplete', 'off');
        el.insert({before: '<div class="regex_field"><pre class="regex regex' + parentId + '" id="regex' + parentId + '"></pre></div>'});
        return $$("pre#regex" + parentId);
    }

};
