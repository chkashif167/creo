var pdc_layer = jQuery.noConflict();
pdc_layer(document).ready(function($){
    PDC_layer = {
        init: function(){
            ////////////First Load /////////////////////
            if(!$('[pdc-block="layer"]').hasClass('loaded')){
                $('[pdc-block="layer"]').draggable({ handle: 'label[pdc-label-drag]' });
                $('[pdc-block="layer"]').on('click','[pdc-layer-info]',function(){
                    var layer_item = $(this).parents('li'),
                        layer_item_index = layer_item.attr('pdc-layer');
                    if(layer_item_index!=0){
                        if($(this).attr('pdc-layer-info')=='del'){
                            layer_item.remove();
                            PDC_layer.removeItemFromLayer(layer_item.attr('name'));
                        }else{
                            PDC_layer.activeobj(layer_item.attr('name'));
                            $('[pdc-block="layer"] li.active').removeClass('active');
                            layer_item.addClass('active');
                        }
                        if($(this).attr('pdc-layer-info')=='lock'){
                            ///////////////Lock item//////////////////////
                            var act = $(this).hasClass('lock') ? true : false;
                            PDC_layer.lock_obj(layer_item.attr('name'),act);
                            $(this).toggleClass('lock');
                        }
                    }
                })
                $('[pdc-block="layer"]').addClass('loaded');
            }
            PDC_layer.load_layer();
            canvas.observe('object:added', PDC_layer.load_layer);
        },
        load_layer_first_time: function(){
            var objects = canvas.getObjects();
            if(objects.length > 0) {
                objects.forEach(function(o) {
                    var html = $('[pdc-block="layer"] pdc-layer="0"');
                    $('[pdc-block="layer"] ul').append(html);
                    var html = '<li>';
                    if(o.type=='text'){
                        html  +=   o.text;
                    }
                    if((o.type=='path-group')||(o.type=='image')){
                        html  +=   '<img src="'+o.isrc+'"/>';
                    }
                    html+='<i class="pdc_layer_delete">Del</i>';
                    html += '</li>';
                });
            }
        },
        objSelect: function (e) {
            if(e.target.get('type')!='group'){
                var active = canvas.getActiveObject();
                if(!active)return;
                var name = active.name;
                PDC_layer.activeobj(name);
                $('[pdc-block="layer"] li.active').removeClass('active');
                $('[pdc-block="layer"] li[name="'+name+'"]').addClass('active');
            }else{
                
            }
            
        },
        lock_obj: function(name,act){
            var objects = canvas.getObjects();
            if((name!='')&&(name!=undefined)){
                for (var i = 0; i < objects.length; i++) {
                    if(objects[i].name==name){
                        objects[i].set({
                            selectable: act
                        });
                        if(!act){
                            canvas.deactivateAll().renderAll();
                            $('[pdc-box="toolbox"]').hide();
                        }
                        canvas.renderAll();
                        console.log(objects[i]);
                    }
                }
            }
        },
        load_final_price: function(){
            //$('#final_price').val();
        },
        activeobj: function(el){
            var objects = canvas.getObjects();
            if((el!='')&&(el!=undefined)){
                for (var i = 0; i < objects.length; i++) {
                    if(objects[i].name==el){
                        canvas.setActiveObject(objects[i]);
                    }
                }
            }
        },
        load_layer: function(){
            var html = $('[pdc-block="layer"] [pdc-layer="0"]').html();
            $('[pdc-block="layer"] ul').html('<li pdc-layer="0">'+html+'</li>');
            var objects = canvas.getObjects();
            if(objects.length > 0) {
                PDC_layer.updatePosition();
                var i = 0,price=0;
                objects.forEach(function(o) {
                    i++; 
                    var name = 'item_'+i;
                    o.set({name:name}); canvas.renderAll();
                    if((o.price=='')||(o.price==undefined)){o.price=0;}
                    price+=parseFloat(o.price);
                    $('[pdc-block="layer"] ul').append('<li pdc-layer="'+i+'" name="'+name+'">'+html+'</li>');
                    if(o.type=='text'){
                        $('[pdc-layer="'+i+'"] [pdc-layer-info="type"]').html(o.text.substring(0,10));
                    }
                    if((o.type=='image')||(o.type=='path-group')){
                        $('[pdc-layer="'+i+'"] [pdc-layer-info="type"]').html('<img src="'+o.isrc+'" />');
                    }
                    $('[pdc-layer="'+i+'"] [pdc-layer-info="price"]').html(parseFloat(o.price).toFixed(2));
                    $('[pdc-layer="'+i+'"] [pdc-layer-info="size"]').html(parseInt(o.width*o.scaleX) + 'X' + parseInt(o.height*o.scaleY));
                    //$('[pdc-layer-info="size"]').html(o.price);
                });
                $('[pdc-block="layer-final"] .price_layer').html(parseFloat(price).toFixed(2));
            }
            PDC_layer.modify_layer();
        },
        modify_layer: function(){
            var active = canvas.getActiveObject();
            if (!active) return;
            var name = active.name;
            $('[pdc-block="layer"] li.active').removeClass('active');
            $('[pdc-block="layer"] li[name="'+name+'"]').addClass('active');
        },
        update_price: function(){
            var objects = canvas.getObjects(),price = 0;
            if(objects.length > 0) {
                objects.forEach(function(o) {
                    price+=parseFloat(o.price);
                })
            }
            $('[pdc-block="layer-final"] .price_layer').html(parseFloat(price).toFixed(2));
        },
        removeItemFromLayer: function(el){
            var objects = canvas.getObjects();
            if((el!='')&&(el!=undefined)){
                for (var i = 0; i < objects.length; i++) {
                    if(objects[i].name==el){
                        canvas.remove(objects[i]);
                        PDC_layer.update_price();
                    }
                }
            }
        },
        updatePosition: function(){
            $('[pdc-layer]').each(function(){
                $(this).attr('pdc-layer',$(this).index());
            })
        }
    }
    $(window).on('keydown', function (e) {
        var key = e.keyCode || e.which;
        if (key == 37) { // left arrow
            return false;
        } else if (key == 38) { // up arrow
            return false;
        } else if (key == 39) { // right arrow
            return false;
        } else if (key == 40) { // down arrow
            return false;
        } else if (key == 46) { // delete key
            PDC_layer.load_layer();
            return false;
        }
    });
})