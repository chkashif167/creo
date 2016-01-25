var FeedExportMapping = {
    rowRemove: function(e)
    {
        e.ancestors()[1].remove();
    },

    rowUp: function(e, c)
    {
        if (c == 'DynamicAttribute') {
            FeedExportDynamicAttribute.rowMove(e, 'up');
        } else {
            FeedExportMapping.rowMove(e, 'up');   
        }
    },

    rowDown: function(e, c)
    {
        if (c == 'DynamicAttribute') {
            FeedExportDynamicAttribute.rowMove(e, 'down');
        } else {
            FeedExportMapping.rowMove(e, 'down');
        }
    },

    rowMove: function (e, direction)
    {
        var tr = e.ancestors()[1];
        var table = tr.parentNode;

        index = table.select('tr').indexOf(tr);
        
        var prev = 1;
        if (index > 0) {
            prev = index - 1; 
        }
        
        var next = table.select('tr').length - 2;
        if (index < table.select('tr').length - 1) {
            next = index + 1;
        } 
            
        prevli = table.select('tr')[prev];
        nextli = table.select('tr')[next];
          
        tr.remove();
            
        switch(direction){
            case 'up':
                prevli.insert({before : tr});
            break;
            case 'down':
                nextli.insert({after : tr});
            break;
        }
    },

    rowAdd: function(url)
    {
        this.request = new Ajax.Request(url, {
            method: 'POST',
            onComplete: function (response) {
                var result = response.responseJSON;
                var row = '<tr><td><button class="button" onclick="FeedExportMapping.rowUp(this); return false;"><span><span>↑</span></span></button><button class="button" onclick="FeedExportMapping.rowDown(this); return false;"><span><span>↓</span></span></button></td><td><input type="text" value="" name="csv[mapping][header][]" class="input-text"></td><td><input type="text" value="" name="csv[mapping][prefix][]" class="input-text"></td><td><select name="csv[mapping][type][]" onchange="FeedExportMapping.changeValueType(this)" style="width:100%;"><option value="attribute" selected="selected">Attribute</option><option value="parent_attribute">Parent Attribute</option><option value="pattern">Pattern</option></select></td><td><input type="text" value="" name="csv[mapping][value_pattern][]" class="input-text" style="display:none;">' + result.value + '</td><td><input type="text" class="input-text" name="csv[mapping][suffix][]" value=""></td><td>' + result.type + '</td><td><input type="text" value="" name="csv[mapping][limit][]" class="input-text"></td><td><button class="button" onclick="FeedExportMapping.rowRemove(this); return false;"><span><span>✖</span></span></button></td></tr>';
                $$('#mapping-table tr').last().insert({'after': row});
            }
        });
    },

    changeValueType: function(e)
    {          
        if (e.value == 'pattern') {
            e.parentNode.parentNode.select('[name="csv[mapping][value_pattern][]"]').first().style.display = 'block';
            e.parentNode.parentNode.select('[name="csv[mapping][value_attribute][]"]').first().style.display = 'none';
        } else {
            e.parentNode.parentNode.select('[name="csv[mapping][value_pattern][]"]').first().style.display = 'none';
            e.parentNode.parentNode.select('[name="csv[mapping][value_attribute][]"]').first().style.display = 'block';
        }
    },

    changeFormat: function (e)
    {
        if (e.value == 'xml') {
            $('tabs_xml_section').parentNode.style.display = 'block';
            $('tabs_csv_section').parentNode.style.display = 'none';
        } else if (e.value == 'csv' || e.value == 'txt') {
            $('tabs_xml_section').parentNode.style.display = 'none';
            $('tabs_csv_section').parentNode.style.display = 'block';
        }
    }
};