if (!Magidev) {
	var Magidev = {};
}

Magidev.Available=Class.create();
Magidev.Available.prototype={
	initialize: function(){
	},
	sort: function(categoryId, saveURL, type){
		var sort=new Magidev.Sort(categoryId, saveURL, type);
		var items=$$('.category-products .item');
		for(var i=items.length-1; i>=0; i--){
			var item=items[i];
			if(item.down('div').readAttribute('data-stock')==0){
				sort.move(item.down(),$('position-'+items.length));
			}
		}
	}
};
var mAvailable=new Magidev.Available();

Magidev.Search = Class.create();
Magidev.Search.prototype = {
	initialize: function () {
	},
	search: function(query,categoryId,url,type){
		if(query.length<2){
			return;
		}
		this.type=type;
		var $this=this;
		new Ajax.Request(
			url + (url.match(new RegExp('\\?')) ? '&isAjax=true' : '?isAjax=true' ),
			{
				parameters: {query:query, category_id:categoryId },
				method: 'post',
				onSuccess: function (response) {
					var result=response.responseText.evalJSON();
					$('add-product-container').update('');
					result.each(function(item){
						var div=new Element('div');
						div.writeAttribute('data-src',item.image);
						div.writeAttribute('data-name',item.name);
						div.writeAttribute('data-price',item.price);
						div.writeAttribute('data-sku',item.sku);
						div.writeAttribute('data-id',item.id);
						div.writeAttribute('data-delete-url',item.delete_url);
						div.writeAttribute('data-edit-url',item.edit_url);
						div.writeAttribute('data-status-url',item.status_url);
						div.writeAttribute('data-quick-edit-url',item.quick_edit_url);
						div.writeAttribute('data-stock',item.is_in_stock);
						div.insert({bottom:new Element('img',{src:item.image, width:'100px', height:'100px'})});
						var info=new Element('div');
						info.insert({bottom:new Element('div',{class:'name'}).update(item.name)});
						info.insert({bottom:new Element('div',{class:'attribute'}).update('Price: '+item.price)});
						info.insert({bottom:new Element('div',{class:'attribute'}).update('SKU: '+item.sku)});
						info.insert({bottom:new Element('div',{class:'attribute stock'}).update('Availability: '+((item.is_in_stock)?'<span class="in">in stock</span>':'<span class="out">out of stock</span>'))});
						info.insert({bottom:new Element('div').insert({
							bottom:new Element('a',{href:'#',class:'add-item-to-category'}).insert({top:new Element('img',{src:'/skin/adminhtml/default/default/images/magidev/sort/add.png'})})
						})});
						$('add-product-container').insert({bottom: div.insert(info)});
					});
					$$('.add-item-to-category').each(function(el){
						el.observe('click', function(e){
							Event.stop(e);
							$this.move(el);
						});
					});
				}
			}
		);
	},
	move: function(el){
		var el=el.up().up().up();
		var container=$$('.position-to-insert')[0];
		container.show();
		container.setStyle({ visibility:'hidden'});
		var containerOffset=Element.viewportOffset(container);
		var elementOffset=Element.viewportOffset(el);
		el.setStyle({zIndex:'10000'});
		new Effect.Move(el,{duration:'0.4',x:(containerOffset.left-elementOffset.left),y:(containerOffset.top-elementOffset.top)-130,mode: 'relative',
			afterFinish: function(){
				var result=this.add(el,container);
				this.save(el.readAttribute('data-id'),result.readAttribute('data-position'));
			}.bind(this),
			afterSetup: function(){
				Effect.ScrollTo(container, { duration:'0.9', offset:(containerOffset.top-elementOffset.top) });
			}
		});
	},
	add: function(el,container){
		var collectionSize=$('category-products-list').readAttribute('data-collection-count');
		var columnCount=$('category-products-list').readAttribute('data-column-count');
		var categoryId=$('category-products-list').readAttribute('data-category-id');
		var saveURL=$('category-products-list').readAttribute('data-save-url');
		var type=$('category-products-list').readAttribute('data-sort-type');
		var div=new Element('div',{class:'product'}).setStyle({position:'relative',id:'productId-'+el.readAttribute('data-id')});
		div.insert({
			bottom:new Element('img',{src:el.readAttribute('data-src')})
		});
		div.insert({
			bottom:new Element('h2',{class:'product-name'}).update(el.readAttribute('data-name')+'<br/><span>SKU: </span>'+el.readAttribute('data-sku')+'<br/><span>Price: </span>'+el.readAttribute('data-price'))
		});
		var actions=new Element('div',{class:'actions'});
		div.insert({
			bottom:actions
		});
		actions.insert({bottom: new Element('a',{rel:el.readAttribute('data-quick-edit-url'),class:'edit-item'}).insert({bottom:new Element('img',{src:'/skin/adminhtml/default/default/images/magidev/sort/tab_edit.png'})})});
		actions.insert({bottom: new Element('a',{href:el.readAttribute('data-edit-url'), target:'_blank'}).insert({bottom:new Element('img',{src:'/skin/adminhtml/default/default/images/icon_edit_address.gif'})})});
		actions.insert({bottom: new Element('a',{rel:el.readAttribute('data-status-url'),class:'disable-item'}).insert({bottom:new Element('img',{src:'/skin/adminhtml/default/default/images/icon-enabled.png'})})});
		actions.insert({bottom: new Element('a',{rel:el.readAttribute('data-delete-url'),class:'delete-item'}).insert({bottom:new Element('img',{src:'/skin/adminhtml/default/default/images/icon_remove_address.gif'})})});


		div.setStyle({opacity:0});

		container.insert(div);
		container.setStyle({ visibility:'visible'});
		container.removeClassName('position-to-insert');
		container.addClassName('item');
		container.addClassName('last');

		var newPosition=(parseInt(container.readAttribute('data-position'))+1);
		var newIndex=(parseInt(container.id.split('-')[1])+1);
		var newContainer=new Element('li',{class:'position-to-insert',id:'position-'+newIndex}).writeAttribute('data-position',newPosition);

		var products=$H($('in_category_products').value.toQueryParams());
		products.set(el.readAttribute('data-id'),newPosition);
		$('in_category_products').value = products.toQueryString();

		newContainer.hide();
		if ( (newIndex-1)%columnCount==0 ){
			var newGrid=new Element('ul',{class:'products-grid'});
			container.up().insert({after:newGrid});
			newGrid.insert({bottom:newContainer});
		} else {
			container.insert({after:newContainer});
		}
		new Effect.Opacity(el, { from: 1.0, to: 0, duration: 0.2 , afterFinish: function(){ el.remove(); }});
		new Effect.Opacity(div, { from: 0, to: 1.0, duration: 0.4 });
		new Magidev.Sort(categoryId,saveURL,type);
		return container;
	},
	save: function(ids,positions){
		var url=$('category-products-list').readAttribute('data-add-url');
		var categoryId=$('category-products-list').readAttribute('data-category-id');
		new Ajax.Request(
				url + (url.match(new RegExp('\\?')) ? '&isAjax=true' : '?isAjax=true' ),
				{
					parameters: {
						category:categoryId,
						'products[]': ids,
						'positions[]':positions
					},
					method: 'post',
					onSuccess: function (response) {

					}
				}
		);
	},
	addAll: function(){
		var ids=new Array();
		var positions=new Array();
		var container={};
		$$('.add-item-to-category').each(function(el,i){
			var el=el.up().up().up();
			container=this.add(el,$$('.position-to-insert')[0]);
			container.show();
			ids[i]=el.readAttribute('data-id');
			positions[i]=container.readAttribute('data-position');
		}.bind(this));
		var containerOffset=Element.viewportOffset(container);
		Effect.ScrollTo(container, { duration:'0.9', offset:(containerOffset.top) });
		this.save(ids,positions);
	}
}
var Search = new Magidev.Search();

Magidev.Sort = Class.create();
Magidev.Sort.prototype = {
	initialize: function (categoryId, saveURL, type) {
		this.categoryId = categoryId;
		this.url = saveURL;
		this.initDragAndDrop();
		this.initActions();
		this.type=(type)?type:'replace';
		this.keyCode=null;
	},
	initActions: function () {
		$$('.disable-item').each(function (item) {
			item.stopObserving();
			item.observe('click', function (e) {
				Event.stop(e);
				this.disableItem(item);
			}.bindAsEventListener(this));
		}.bind(this));
		$$('.delete-item').each(function (item) {
			item.stopObserving();
			item.observe('click', function (e) {
				Event.stop(e);
				this.deleteItem(item);
			}.bindAsEventListener(this));
		}.bind(this));
		$$('.edit-item').each(function (item) {
			item.stopObserving();
			item.observe('click', function (e) {
				Event.stop(e);
				this.editItem(item);
			}.bindAsEventListener(this));
		}.bind(this));
		$$('.item').each(function(item){
			item.observe('click',function(){
				this.initMultiSelect(item);
			}.bindAsEventListener(this));
		}.bind(this));
		document.observe('keydown', function(e){
			if(e.keyCode==17){
				this.keyCode=17;
			}
		}.bindAsEventListener(this));
		document.observe('keyup', function(e){
		  this.keyCode=null;
		}.bindAsEventListener(this));
		document.observe('click', function(e){
			if(this.keyCode!=17){
				$$('.item').each(function(item){
					item.removeClassName('selected');
				});
				return 0;
			}
		}.bindAsEventListener(this));
	},
	initMultiSelect: function(item){
		if(this.keyCode!=17){
			$$('.item').each(function(item){
				item.removeClassName('selected');
			});
			return 0;
		}
		item.addClassName('selected');
	},
	editItem: function(item){
		var url = item.rel;
		var $this=this;
		new Ajax.Request(
				url + (url.match(new RegExp('\\?')) ? '&isAjax=true' : '?isAjax=true' ),
				{
					parameters: {},
					method: 'post',
					onSuccess: function (response) {
						var result=response.responseText.evalJSON();
						if( result.entity_id ){
							$this.displayWindow(result.sys_url,item);
							$('m.quick.title').value=result.name;
							$('m.quick.sku').value=result.sku;
							$('m.quick.price').value=result.price;
							if(result.special_price){
								$('m.quick.s.price').value=result.special_price;
							}
							$('m.quick.short.description').value=result.short_description;
						} else {
							alert('Error: '+response.error);
						}
					}
				}
		);

	},
	displayWindow: function(url,item){
		var $this=this;
		var dialogWindow = Dialog.confirm($('edit-form').innerHTML, {
			closable:true,
			resizable:false,
			draggable:false,
			windowClassName:'popup-window',
			width:800,
			height:400,
			zIndex:1000,
			recenterAuto:false,
			hideEffect:Element.hide,
			showEffect:Element.show,
			okLabel: 'Save',
			id:'browser_window',
			onOk:function () {
				$this.quickSave(url,dialogWindow,item);
			},
			onCancel: function(){
				dialogWindow.close();
			}
		});
	},
	quickSave: function(url,win,item){
		new Ajax.Request(
				url + (url.match(new RegExp('\\?')) ? '&isAjax=true' : '?isAjax=true' ),
				{
					parameters: {
						name:$('m.quick.title').value,
						sku:$('m.quick.sku').value,
						price:$('m.quick.price').value,
						special_price:$('m.quick.s.price').value,
						short_description:$('m.quick.short.description').value
					},
					method: 'post',
					onSuccess: function (response) {
						var result=response.responseText.evalJSON();
						if( result.entity_id ){
							item.up().previous('h2').update(
									result.name+
									'<br/><span>SKU:</span> '+result.sku+
									'<br/><span>Price:</span> '+result.price
							);
							win.close();
						} else {
							alert('Error: '+response.error);
						}
					}
				}
		);
	},
	disableItem: function(item){
		var url = item.rel;
		new Ajax.Request(
				url + (url.match(new RegExp('\\?')) ? '&isAjax=true' : '?isAjax=true' ),
				{
					parameters: {},
					method: 'post',
					onSuccess: function (response) {
						if (response.responseText == 1) {
							item.up().up().removeClassName('disabled-item');
						} else {
							item.up().up().addClassName('disabled-item');
						}
					}
				}
		);
	},
	deleteItem: function(item){
		var url = item.rel;
		var self=this;
		new Ajax.Request(
				url + (url.match(new RegExp('\\?')) ? '&isAjax=true' : '?isAjax=true' ),
				{
					parameters: {},
					method: 'post',
					onSuccess: function (response) {
						if (response.responseText == 1) {
							// remove product from hash
							var deleteProductId=item.up().up().readAttribute('data-id');
							var products=$H($('in_category_products').value.toQueryParams());
							var columnCount=$('category-products-list').readAttribute('data-column-count');
							products.unset(deleteProductId);
							$('in_category_products').value = products.toQueryString();
							try{
								// remove product from list
								var startPosition=item.up().up().up().id.split('-')[1];
								var position=startPosition;
								$('position-'+(startPosition)).down('div.product').remove();

								while( $('position-'+(startPosition++))!=null ){
									if(!$('position-'+(startPosition)).down('div.product')){
										$('position-'+(startPosition-1)).remove();
										var positionToInsert=$('position-'+(startPosition));
										positionToInsert.writeAttribute('id','position-'+(startPosition-1));
										if(positionToInsert.up().previous().childElements().length<columnCount){
											var positionClone=positionToInsert.clone(true);
											positionToInsert.up().previous().appendChild(positionClone);
											positionToInsert.up().remove();
										}
										break;
									}
									var product=$('position-'+(startPosition)).down('div.product').clone(true);
									$('position-'+(startPosition-1)).appendChild(product);
									$('position-'+(startPosition)).down('div.product').remove();
									self.addDraggable(product);
								}
							} catch ( e ){
								console.log(e);
							}
							self.initActions();
						}
					}
				}
		);
	},
	initDragAndDrop: function () {
		var self = this;
		$$('.item').each(function (el) {
			Droppables.add(el, {
				accept: 'product',
				hoverclass: 'hover',
				onDrop: function (dragged, dropped, event) {
					if( dragged.up().hasClassName('selected') ){
						var selected=$$('.item.selected');
						var positionToInsert=parseInt(dropped.id.split('position-')[1]);
						var newPositions=null;
						if(parseInt(dragged.up().id.split('position-')[1])>parseInt(dropped.id.split('position-')[1])){
							for(var i=0; i<selected.length; i++ ){
								selected[i].removeClassName('selected');
								newPositions=self.move(selected[i].down('div'), $('position-'+positionToInsert) );
								positionToInsert++;
							}
						} else {
							for(var i=selected.length-1; i>=0; i-- ){
								selected[i].removeClassName('selected');
								newPositions=self.move(selected[i].down('div'), $('position-'+positionToInsert) );
								positionToInsert--;
							}
						}
						self.updatePosition(newPositions);
					} else {
						self.updatePosition(self.move(dragged, dropped));
					}
				}
			});
		});
		$$('.product').each(function (el) {
			self.addDraggable(el);
		});
	},
	move: function(dragged, dropped){
		var self=this;
		if (dragged.up().id == dropped.id) {
			dragged.setStyle({borderColor: '#FFF'});
			return 0;
		}
		if( self.type=='replace' ){
			var product1 = dropped.down('div').clone(true);
			product1.stopObserving();
			dragged.up().appendChild(product1);
			dropped.down('div').remove();
			self.addDraggable(product1);
			var product2 = dragged.clone(true);
			product2.stopObserving();
			dragged.remove();
			dropped.appendChild(product2.setStyle({
				position: 'inherit',
				top: 'auto',
				left: 'auto',
				opacity: 1,
				zIndex: 0,
				borderColor: '#FFF'
			}));
			self.addDraggable(product2);
			self.updatePosition(new Hash({
				product1: product1.readAttribute('data-id'),
				position1: product1.up().readAttribute('data-position'),
				product2: product2.readAttribute('data-id'),
				position2: product2.up().readAttribute('data-position'),
				categoryId: self.categoryId
			}));
			var products=$H($('in_category_products').value.toQueryParams());
			products.set(product1.readAttribute('data-id'),product1.up().readAttribute('data-position'));
			products.set(product2.readAttribute('data-id'),product2.up().readAttribute('data-position'));
			$('in_category_products').value = products.toQueryString();

			product1.down('div.actions').down('a.disable-item').observe('click',function(e){ Event.stop(e); self.disableItem(product1.down('div.actions').down('a.disable-item')); });
			product1.down('div.actions').down('a.delete-item').observe('click',function(e){ Event.stop(e); self.deleteItem(product1.down('div.actions').down('a.delete-item')); });
			product1.down('div.actions').down('a.edit-item').observe('click',function(e){ Event.stop(e); self.editItem(product1.down('div.actions').down('a.edit-item')); });

			product2.down('div.actions').down('a.disable-item').observe('click', function(e){ Event.stop(e); self.disableItem(product2.down('div.actions').down('a.disable-item')); });
			product2.down('div.actions').down('a.delete-item').observe('click', function(e){ Event.stop(e); self.deleteItem(product2.down('div.actions').down('a.delete-item')); });
			product2.down('div.actions').down('a.edit-item').observe('click', function(e){ Event.stop(e); self.editItem(product2.down('div.actions').down('a.edit-item')); });
		} else {
			var products=$H($('in_category_products').value.toQueryParams());
			var newPositions=$H({});
			var endPosition=parseInt(dropped.id.split('-')[1]);
			var startPosition=parseInt(dragged.up().id.split('-')[1]);
			if( startPosition<endPosition ){
				while( (startPosition++)<endPosition ){
					var tmpProduct=$('position-'+(parseInt(startPosition))).down('div.product').clone(true);
					tmpProduct.stopObserving();
					$('position-'+(startPosition-1)).appendChild(tmpProduct);
					$('position-'+(startPosition)).down('div.product').remove();
					self.addDraggable(tmpProduct);
					newPositions.set(tmpProduct.readAttribute('data-id'),tmpProduct.up().readAttribute('data-position'));
					products.set(tmpProduct.readAttribute('data-id'),tmpProduct.up().readAttribute('data-position'));
				}
			} else {
				while( (startPosition--)>endPosition ){
					var tmpProduct=$('position-'+(parseInt(startPosition))).down('div.product').clone(true);
					tmpProduct.stopObserving();
					$('position-'+(startPosition+1)).appendChild(tmpProduct);
					$('position-'+(startPosition)).down('div.product').remove();
					self.addDraggable(tmpProduct);
					newPositions.set(tmpProduct.readAttribute('data-id'),tmpProduct.up().readAttribute('data-position'));
					products.set(tmpProduct.readAttribute('data-id'),tmpProduct.up().readAttribute('data-position'));
				}
			}
			var product2 = dragged.clone(true);
			product2.stopObserving();
			dragged.remove();
			dropped.appendChild(product2.setStyle({
				position: 'inherit',
				top: 'auto',
				left: 'auto',
				opacity: 1,
				zIndex: 0,
				borderColor: '#FFF'
			}));
			newPositions.set(product2.readAttribute('data-id'),product2.up().readAttribute('data-position'));
			products.set(product2.readAttribute('data-id'),product2.up().readAttribute('data-position'));
			$('in_category_products').value = products.toQueryString();
			self.addDraggable(product2);
			self.initActions();
			newPositions.set('categoryId', self.categoryId );

			return newPositions;
		}
	},
	addDraggable: function (el) {
		new Draggable(el, {
			scroll: window,
			revert: true,
			starteffect: function (el) {
				new Effect.Morph(el, {style: 'border-color:#849ba3;', duration: 0.7})
			},
			reverteffect: function (el) {
				new Effect.Morph(el, {style: 'border-color:#FFF; left:0; top:0;', duration: 0.7});
			}
		});
	},
	updatePosition: function (params) {
		new Ajax.Request(this.url, {
			parameters: params,
			evalScripts: true,
			onSuccess: function (transport) {
				try {
					if (transport.responseText.isJSON()) {
						var response = transport.responseText.evalJSON();
					}
				} catch (e) {
					alert('error');
				}
			}
		});
	}
};


