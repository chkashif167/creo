jQuery.noConflict();
jQuery(function ($) {

    Dashboard = Backbone.View.extend({
        events: {
            'click .UI-ADD-WIDGET': 'addWidget'
        },

        initialize: function (el) {
            var self = this;
            self.setElement(el);

            self.dashboardGrid = new DashboardGrid($('.UI-DASHBOARD-GRID'));

            _.bindAll(self);
        },

        addWidget: function () {
            var self = this;
            self.dashboardGrid.initWidget(parseInt(new Date().getTime()), {size_x: 10, size_y: 5}, true);
        }
    });

    DashboardGrid = Backbone.View.extend({
        widgets: {},

        events: {
            'change .UI-DASHBOARD-LOCK': 'toogleLock'
        },

        initialize: function (el) {
            var self = this;
            self.setElement(el);

            self.url = $(el).attr('data-url');
            self.id = $(el).attr('data-id');
            self.editable = $(el).attr('data-editable');
            self.storeIds = $(el).attr('data-store-ids');

            self.gridster = $('>ul', self.$el).gridster({
                widget_margins: [5, 5],
                widget_base_dimensions: [20, 20],
                serialize_params: function ($w, w) {
                    var id = w.el.attr('wid');

                    widget = self.widgets[id];
                    widget.params.id = id;
                    widget.params.col = w.col;
                    widget.params.row = w.row;
                    widget.params.size_x = w.size_x;
                    widget.params.size_y = w.size_y;

                    return widget.params;
                },
                draggable: {
                    stop: function (e, ui, $widget) {
                        self.save();
                    },
                },
                resize: {
                    enabled: true,
                    stop: function (e, ui, $widget) {
                        self.save();
                    },
                }
            }).data('gridster');

            if (Cookie.read('dashboard_lock') == 'true') {
                $('.UI-DASHBOARD-LOCK').attr('checked', true);
            }

            self.toogleLock();

            if (!self.editable) {
                self.gridster.disable();
                self.gridster.disable_resize();
            }

            self.load();
        },

        load: function () {
            var self = this;

            $.ajax({
                type: 'GET',
                url: self.url,
                data: {cmd: 'list', dashboard: self.id},
                dataType: 'json',
                success: function (response) {
                    if (response.error != undefined) {
                        alert(response.error);
                        return;
                    }

                    $.each(response['dashboard'], function (id, widget) {
                        self.initWidget(id, widget, false);
                    });

                    self.refresh();
                }
            });
        },

        save: function () {
            var self = this;

            var self = this;

            $.ajax({
                type: 'POST',
                url: self.url,
                data: {cmd: 'save', dashboard: self.id, grid: self.gridster.serialize()},
                dataType: 'json',
                success: function (response) {
                    if (response.error != undefined) {
                        alert(response.error);
                        return;
                    }
                }
            });
        },

        initWidget: function (id, widget, isNew) {
            var self = this;
            var html = $('<li/>')
                .html($('<div/>').attr('id', id))
                .attr('wid', id);

            if (widget.size_x == undefined) {
                widget.size_x = 10;
            }
            if (widget.size_y == undefined) {
                widget.size_y = 10;
            }

            var gridsterWidget = [html, parseInt(widget.size_x), parseInt(widget.size_y), parseInt(widget.col), parseInt(widget.row)];

            var widgetObj = {};
            widgetObj.params = widget;

            widgetObj.el = self.gridster.add_widget.apply(self.gridster, gridsterWidget);

            widgetObj.$el = $('#' + id, widget.el);
            widgetObj.id = id;

            self.widgets[id] = widgetObj;

            widgetObj.view = new DashboardWidget(widgetObj, self);

            if (isNew) {
                widgetObj.view.settings();
            }
        },

        toogleLock: function () {
            var self = this;
            var lock = $('.UI-DASHBOARD-LOCK').attr('checked') ? true : false;

            if (lock) {
                self.gridster.disable();
                self.gridster.disable_resize();
            } else {
                self.gridster.enable();
                self.gridster.enable_resize();
            }

            Cookie.write('dashboard_lock', lock, 40000000);
        },

        refresh: function () {
            var self = this;

            self.updateNextWidget(null);

            setInterval(function () {
                self.updateNextWidget(null);
            }, 60000);
        },

        updateNextWidget: function (id) {
            var self = this;

            var ids = [];

            $.each(self.widgets, function (key, value) {
                ids.push(key);
            });

            var next = ids[($.inArray(id, ids) + 1) % ids.length];
            var prev = ids[($.inArray(id, ids) - 1 + ids.length) % ids.length];

            if (next && $.inArray(id, ids) < $.inArray(next, ids)) {
                var widget = self.widgets[next];

                var d = widget.view.refresh(!widget.view.loaded);
                d.then(function () {
                    self.updateNextWidget(next);
                });
            }
        }
    });

    DashboardWidget = Backbone.View.extend({
        loaderEl: '<div class="spinner"><div class="b1"></div><div class="b2"></div><div class="b2"></div></div>',
        loaded: false,

        controls: {},

        initialize: function (widget, dashboard) {
            var self = this;
            _.bindAll(self);

            self.setElement(widget.$el);
            self.dashboard = dashboard;
            self.widget = widget;

            if (self.dashboard.editable) {
                self.controls = {
                    settings: {
                        label: 'configure',
                        callback: 'settings'
                    },
                    refresh: {
                        label: 'refresh',
                        callback: 'refresh'
                    },
                    close: {
                        label: 'remove',
                        callback: 'remove'
                    }
                };
            } else {
                self.controls = {
                    refresh: {
                        label: 'refresh',
                        callback: 'refresh'
                    }
                };
            }

            self.$el.html(self._baseHtml());

            self.$controls = $('.widget-controls', self.$el);
            self.$header = $('.widget-header', self.$el);
            self.$content = $('.widget-content', self.$el);

            $.each(self.controls, function (id, control) {
                var html = '<a class="widget-control">' + control.label + '</a>';
                $(html).prependTo(self.$controls).click(function () {
                    self[control.callback]();
                });
            });
        },

        _baseHtml: function () {
            var html = '';
            html += '<div class="widget-wrapper">';
            html += '    <div class="widget-controls"></div>';
            html += '    <div class="widget-header"></div>';
            html += '    <div class="widget-content"></div>';
            html += '</div>';

            return html;
        },

        refresh: function (loader) {
            var self = this;

            if (loader == undefined) {
                loader = true;
            }

            if (self.widget.params.widget !== undefined) {
                self.loader(loader);

                return $.ajax({
                    type: 'GET',
                    url: self.dashboard.url,
                    data: {
                        cmd: 'load',
                        dashboard: self.dashboard.id,
                        id: self.id,
                        store_ids: self.dashboard.storeIds
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.error != undefined) {
                            return;
                        }

                        self.$header.html(response.title);
                        self.$content.html(response.content);
                        self.loaded = true;

                        self.loader(false);
                    }
                });
            }

            return false;
        },

        settings: function () {
            var self = this;

            var settings = new DashboardWidgetSettings(self);
        },

        remove: function () {
            var self = this;
            self.dashboard.gridster.remove_widget.apply(self.dashboard.gridster, self.$el.closest('li'));
            self.dashboard.save();
        },

        loader: function (show) {
            var self = this;
            if (show) {
                self.$el.addClass('loading');
                self.$el.parent().append(self.loaderEl);
            } else {
                $('.spinner', self.$el.parent()).remove();
                self.$el.removeClass('loading');
            }
        }
    });

    DashboardWidgetSettings = Backbone.View.extend({

        events: {
            'click .UI-SAVE': 'onSave',
            'click .UI-CANCEL': 'onCancel',
            'change .UI-WIDGET-SELECTOR': 'onChangeWidget'
        },

        initialize: function (widgetView) {
            var self = this;
            self.widgetView = widgetView;

            self.widgetView.loader(true);

            $.ajax({
                type: 'GET',
                url: self.widgetView.dashboard.url,
                data: {
                    cmd: 'settings',
                    id: self.widgetView.id,
                    dashboard: self.widgetView.dashboard.id
                },
                dataType: 'json',
                success: function (response) {

                    if (response.error != undefined) {
                        alert(response.error);
                        return;
                    }

                    self.dialogWindow = Dialog.info(response.content, {
                        draggable: true,
                        resizable: true,
                        closable: true,
                        className: 'magento',
                        windowClassName: 'popup-window dashboard-settings',
                        title: 'Edit Widget',
                        width: 500,
                        height: 400,
                        zIndex: 1000,
                        recenterAuto: false,
                        hideEffect: Element.hide,
                        showEffect: Element.show,
                    });

                    self.widgetView.loader(false);

                    self.setElement($(self.dialogWindow.element));

                    _.bindAll(self);
                }
            });
        },

        onChangeWidget: function () {
            var self = this;
            var widget = $('.UI-WIDGET-SELECTOR').val();

            $.ajax({
                type: 'GET',
                url: self.widgetView.dashboard.url,
                data: {
                    cmd: 'settings',
                    id: self.widgetView.id,
                    dashboard: self.widgetView.dashboard.id,
                    widget: widget
                },
                dataType: 'json',
                success: function (response) {
                    if (response.error != undefined) {
                        alert(response.error);
                        return;
                    }

                    $('#modal_dialog_message', self.$el).html(response.content);
                }
            });
        },

        onSave: function () {
            var self = this;

            var params = $('input, select', self.$el).serializeObject();
            self.widgetView.loader(true);
            $.ajax({
                type: 'POST',
                url: self.widgetView.dashboard.url,
                data: {
                    cmd: 'settings',
                    id: self.widgetView.id,
                    dashboard: self.widgetView.dashboard.id,
                    settings: params
                },
                dataType: 'json',
                success: function (response) {
                    if (response.error != undefined) {
                        alert(response.error);
                        return;
                    }

                    self.widgetView.widget.params = params;
                    self.widgetView.loader(false);
                    self.widgetView.refresh();
                    self.dialogWindow.close();
                }
            });
        },

        onCancel: function () {
            var self = this;
            self.dialogWindow.close();
        },
    });

    $.fn.serializeObject = function () {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function () {
            this.name = this.name.replace('[]', '');
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };

    $(function () {
        var dashboard = new Dashboard('.UI-DASHBOARD-CONTAINER');
    });

});