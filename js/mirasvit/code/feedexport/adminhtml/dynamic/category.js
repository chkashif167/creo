var FeedExportDynamicCategory = {

    init: function()
    {
        var self = this;

        $$('.mapping-input').each(function(item) {
            Event.observe(item, 'keyup', function(e) {
                var input = e.currentTarget;
                self.applyPlaceholder(input);
            });
        });

        $$('.category-mapping .toggle').each(function(item) {
            Event.observe(item, 'click', function(e) {
                self.toggleCategories(e.currentTarget);
            });
        });

        self.applyPlaceholders();
    },

    toggleCategories: function(item, type)
    {
        var self = this;

        if (type === undefined) {
           if (item.hasClassName('open')) {
               type = 'show';
           } else {
               type = 'hide';
           }
        }

        var input  = item.parentElement.select('.mapping-input').first();
        var id     = input.readAttribute('data-id');
        var childs = $$('[data-parent="' + id + '"]');

        childs.each(function(child) {
            if (type == 'hide') {
                var toggle = child.parentElement.parentElement.select('.toggle').first();
                if (toggle.hasClassName('close')) {
                    self.toggleCategories(toggle, type);
                }

                child.parentElement.parentElement.hide();
            }
            else {
                child.parentElement.parentElement.show();
            }
        });

        if (type == 'show') {
            item.addClassName('close').removeClassName('open');
            self.applyPlaceholder(input);
        } else {
            item.addClassName('open').removeClassName('close');
        }
    },

    applyPlaceholders: function()
    {
        var self = this;

        // $$('.mapping-input').each(function(input) {
        //     self.applyPlaceholder(input);
        // });
    },

    applyPlaceholder: function(input)
    {
        var self = this;

        if (input.value === '') {
            var value = self.getParentValue(input);
            if (value !== '') {
                input.writeAttribute('placeholder', value);
            } else {
                input.removeAttribute('placeholder');
            }
        }

        var id     = input.readAttribute('data-id');
        var childs = $$('[data-parent="' + id + '"]');
        childs.each(function(child) {
            if (child.parentElement.parentElement.visible()) {
                self.applyPlaceholder(child);
            }
        });
    },

    getParentValue: function(input)
    {
        var self        = this;
        var parentId    = input.readAttribute('data-parent');
        var parentInput = $$('[data-id="' + parentId + '"]').first();

        if (parentInput === undefined) {
            return '';
        }

        var val = parentInput.value;

        if (val) {
            return val;
        } else {
            return parentInput.readAttribute('placeholder');
        }
    },
};