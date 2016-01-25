var FeedExportDynamicAttribute = {
    init: function()
    {
        this.conditionRow = '<tr class="row" data-key="CID">' + $$('#conditions-table #row-template')[0].cloneNode(true).innerHTML + '</tr>';
    },

    addConditionRow: function()
    {
        this.cntCond++;

        var clone = this.conditionRow;
        clone = this.prepareHtml(clone, null);

        this.reset(clone);

        $$('#conditions-table').last().insert(clone);

    },

    removeConditionRow: function(e)
    {
        e.ancestors()[1].remove();
    },

    changeConditionOutputType: function(e)
    {
        if (e.value == 'pattern') {
            e.ancestors()[1].select('.values').last().select('input').first().style.display = 'block';
            e.ancestors()[1].select('.values').last().select('select').first().style.display = 'none';
        } else {
            e.ancestors()[1].select('.values').last().select('input').first().style.display = 'none';
            e.ancestors()[1].select('.values').last().select('select').first().style.display = 'block';
        }
    },

    addSubConditionRow: function (e)
    {
        var table = e.ancestors()[0].select('table').last();
        var tr = table.select('tr').last();

        if (!tr) {
            var tableHidden = e.ancestors()[3].select('#row-template table').last();
            tr = tableHidden.select('tr').last();
        }

        var clone = tr.cloneNode(true);
        this.reset(clone);
        table.insert(clone);
    },

    removeSubConditionRow: function(e)
    {
        e.ancestors()[1].remove();
    },

    changeAttribute: function(e)
    {
        var self = this;
        var data = {attribute: e.value};
        var url  = this.changeAttributeUrl;

        new Ajax.Request(url, {
            parameters: data,
            loaderArea: e,
            onSuccess: function(transport) {
                if (transport.responseText.isJSON()) {
                    var response = transport.responseText.evalJSON();
                    e.ancestors()[1].select('td')[1].update(self.prepareHtml(response.condition, e));
                    e.ancestors()[1].select('td')[2].update(self.prepareHtml(response.value, e));
                }
            }
        });
    },

    prepareHtml: function(html, element)
    {
        var cid = 'CID-' + new Date().getTime();
        if (element !== null) {
            cid = element.up('.row').readAttribute('data-key');
        }
        html = html.replace(/CID/g, cid);

        return html;
    },

    reset: function (html)
    {
        if ($(html)) {
            var selects = $(html).getElementsByTagName('select');
            for (var i = 0; i < selects.length; i++) {
                var item = selects[i];
                item.selectedIndex = 0;
            }

            var inputs = $(html).getElementsByTagName('input');
            for (var i = 0; i < inputs.length; i++) {
                var item = inputs[i];
                item.value = '';
            }
        }
    },

    rowMove: function (e, direction)
    {
        var tr = e.ancestors()[1];

        var table = tr.parentNode;
        
        index = table.select('tr.row').indexOf(tr);
        var prev = 1;
        if (index > 0) {
            prev = index - 1; 
        }
        
        var next = table.select('tr.row').length - 2;
        if (index < table.select('tr.row').length - 1) {
            next = index + 1;
        }
            
        prevli = table.select('tr.row')[prev];
        nextli = table.select('tr.row')[next];
          
        tr.remove();
            
        switch(direction){
            case 'up':
                prevli.insert({before : tr});
            break;
            case 'down':
                nextli.insert({after : tr});
            break;
        }
    }
};