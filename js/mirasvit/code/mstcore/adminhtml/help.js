document.observe("dom:loaded", function() {
    $$("button.mstcore-help-button").each(function(button) {
        new Tooltip(button, {mouseFollow: true, hideDuration: 0, appearDuration: 0, delay:0});
    });

    $$("div.mst-config .hint").each(function(hint) {
        var text = hint.parentElement.parentElement.select("p.note span")[0].innerHTML;
        hint.writeAttribute("title", text);

        new Tooltip(hint, {mouseFollow: true, hideDuration: 0, appearDuration: 0, delay:0});
    });
});
