var SearchIndexAttributes = {
    addItem: function()
    {
        var html = '<tr>' + $('attributes_template').innerHTML + '</tr>' ;
        Element.insert($('attributes_container'), {'bottom': html});
    },

    deleteItem : function(e) {
        var tr = Event.findElement(e, 'tr');
        if (tr) {
            tr.remove();
        }
    },
};