var EmailTriggerChain = new Class.create();
EmailTriggerChain.prototype = {
    initialize : function(parent, newChildUrl){
        this.container = $('chain-container')
        this.parent = Element.down(this.parent, '.chain');
        this.newChildUrl  = newChildUrl;
        this.shownElement = null;
        this.updateElement = null;
        this.chooserSelectedItems = $H({});
        this.readOnly = false;

        var elems = this.parent.getElementsByClassName('chain-row');
        for (var i = 0; i < elems.length; i++) {
            this.initParam(elems[i]);
        }

        var add = Element.down(this.container, '.add-email-chain-row');
        Event.observe(add, 'click', this.addEmailChainRow.bind(this, this.parent));
    },

    initParam: function (container)
    {
        var elems = container.getElementsByClassName('chain-param');
        for (var i = 0; i < elems.length; i++) {
            var param = elems[i];

            param.rulesObject = this;
            var label = Element.down(param, '.label');
            if (label) {
                Event.observe(label, 'click', this.showParamInputField.bind(this, param));
            }

            var elem = Element.down(param, '.element');
            if (elem) {
                elem = elem.down('.element-value-changer');
                elem.param = param;
                if (!elem.multiple) {
                    Event.observe(elem, 'change', this.hideParamInputField.bind(this, param));
                }
                Event.observe(elem, 'blur', this.hideParamInputField.bind(this, param));
            }

            this.hideParamInputField(param, null);
        }

        var remove = container.getElementsByClassName('chain-row-remove');
        if (remove) {
            Event.observe(remove[0], 'click', this.removeRowEntry.bind(this, container));
        }

        var expand = container.getElementsByClassName('chain-row-expand');
        if (expand) {
            Event.observe(expand[0], 'click', this.toggleOptions.bind(this, container));
        }

        var collapse = container.getElementsByClassName('chain-row-collapse');
        if (collapse) {
            Event.observe(collapse[0], 'click', this.toggleOptions.bind(this, container));
        }
    },

    toggleOptions: function (container, event)
    {
        var options = container.down('.options');
        
        if (!options) {
            return;
        }

        if (options.style.display == 'block') {
            options.style.display = 'none';
            container.getElementsByClassName('chain-row-collapse')[0].style.display = 'none';
            container.getElementsByClassName('chain-row-expand')[0].style.display   = 'inline';
        } else {
            options.style.display = 'block';
            container.getElementsByClassName('chain-row-collapse')[0].style.display = 'inline';
            container.getElementsByClassName('chain-row-expand')[0].style.display   = 'none';
        }
    },

    showParamInputField: function (container, event)
    {
        if (this.readOnly) {
            return false;
        }

        if (this.shownElement) {
            this.hideParamInputField(this.shownElement, event);
        }

        Element.addClassName(container, 'chain-param-edit');
        var elemContainer = Element.down(container, '.element');

        var elem = Element.down(elemContainer, 'input.input-text');
        if (elem) {
            elem.focus();
            if (elem && elem.id && elem.id.match(/__value$/)) {
                this.updateElement = elem;
                //this.showChooser(container, event);
            }

        }

        var elem = Element.down(elemContainer, '.element-value-changer');
        if (elem) {
           elem.focus();
        }

        this.shownElement = container;
    },

    hideParamInputField: function (container, event)
    {
        Element.removeClassName(container, 'chain-param-edit');
        var label = Element.down(container, '.label'), elem;

        if (!container.hasClassName('chain-param-new-child')) {
            elem = Element.down(container, '.element-value-changer');
            if (elem && elem.options) {
                var selectedOptions = [];
                for (i=0; i<elem.options.length; i++) {
                    if (elem.options[i].selected) {
                        selectedOptions.push(elem.options[i].text);
                    }
                }

                var str = selectedOptions.join(', ');
                label.innerHTML = str!='' ? str : '...';
            }

            elem = Element.down(container, 'input.input-text');
            if (elem) {
                var str = elem.value.replace(/(^\s+|\s+$)/g, '');
                elem.value = str;
                if (str=='') {
                    str = '...';
                } else if (str.length > 30) {
                    str = str.substr(0, 30) + '...';
                }
                label.innerHTML = str.escapeHTML();
            }
        } else {
            elem = Element.down(container, '.element-value-changer');
            if (elem.value) {
                this.addChainNewChild(elem);
            }
            elem.value = '';
        }

        if (elem && elem.id && elem.id.match(/__value$/)) {
            this.hideChooser(container, event);
            this.updateElement = null;
        }

        this.shownElement = null;
    },

    addEmailChainRow: function (parent)
    {
        var row   = parent.getElementsByClassName('chain-row');
        var clone = row[0].cloneNode(true);
        var max   = 0;
        var i     = 0;

        var inputs = Selector.findChildElements(parent, $A(['input', 'select']));
        if (inputs.length) {
            inputs.each(function(el) {
                i = el.name.match(/([0-9]+)/);
                i = 1 * i[0];
                max = i > max ? i : max;
            });
        }
        
        var childrenInputs = Selector.findChildElements(clone, $A(['input', 'select']));
        childrenInputs.each(function(el) {
            var name = el.name;
            if (name.indexOf('new-') >= 0) {
                name = name.replace(/(new-[0-9]{1,3})/, 'new-' + (max + 1));
            } else {
                name = name.replace(/([0-9]{1,3})/, 'new-' + (max + 1));
            }

            el.name = name;
        });
        
        clone.style.display = 'block';
        parent.insertBefore(clone, $(parent).up('li'));
        
        this.onAddNewEntryComplete(clone);
    },

    onAddNewEntryComplete: function (newElem)
    {
        this.initParam(newElem);
    },

    removeRowEntry: function (container, event)
    {
        if (container) {
            container.remove();
        }
    }
}
