var designForm = jQuery.noConflict();
designForm.fn.ForceNumericOnly = function() {
	return this.each(function()
	{
		designForm(this).keydown(function(e)
		{
			var key = e.charCode || e.keyCode || 0;
			// allow backspace, tab, delete, arrows, numbers
				  // and keypad numbers ONLY
			return (
				key == 8 ||
				key == 9 ||
				key == 46 ||
				key == 190 ||
				key == 110 ||
				(key >= 37 && key <= 40) ||
				(key >= 48 && key <= 57) ||
				(key >= 96 && key <= 105));
		});
	});
};
designForm(function($) {
	var baseUrl = $("#base_url").val();
	var mediaUrl = $("#media_url").val();
	
	var DesignAction = function(selector) {
		this.selector = selector;
		this.position = null;
		this.checkAll = function() {
			$(selector).each(function(){
				$(this).prop('checked', true);
			});
		};
		this.unCheckAll = function() {
			$(selector).each(function(){
				$(this).prop('checked', false);
			});
		};
		this.getSelectedItem = function() {
			var selected = new Array();
			$('.design-item').each(function() {
				if ($(this).is(':checked')) {
					selected.push($(this).attr('id'));
				}
			});
			return selected;
		};
		this.updatePosition = function(items) {
			
			var data = new Array();
			var posId = posVal = "";
			for (var i =0; i < items.length; i ++) {
				posId = items[i].split('_')[1];
				posVal = $('#' + items[i]).val();
				if ($.inArray(posId, data) == -1) {
					data.push(posId + '_' + posVal);
				}
			}
			this.applyAction('position', data);
		};
		this.deleteItem = function(items) {
			var ids = new Array();
			for (var i = 0; i < items.length; i++) {
				ids.push(items[i].split('_')[1])
			}
			this.applyAction('delete', ids);
		};
		this.disableItem = function(items) {
			var ids = new Array();
			for (var i = 0; i < items.length; i++) {
				ids.push(items[i].split('_')[1])
			}
			this.applyAction('disable', ids);
		};
		this.enableItem = function(items) {
			var ids = new Array();
			for (var i = 0; i < items.length; i++) {
				ids.push(items[i].split('_')[1])
			}
			this.applyAction('enable', ids);
		};
		this.applyAction = function (action, data) {
			$.ajax({
				type : "POST",
				url : baseUrl + 'pdp/index/updateDesign',
				data : {action: action, data: data},
				beforeSend : function () {
					$("#loading-mask").attr("style","left: -2px; top: 0px; width: 1034px; height: 833px;");
					$("#loading-mask").show();
				},
				error : function () {
					console.log('Transfer error!');
				},
				success : function (response) {
					if (response == "") {
						console.log('Done');
						location.reload();
						$('#loading-mask').hide();
					} else {
						alert('There are some errors!');
						location.reload();
					}
				}
			});
		};
	};
	
	var Action = new DesignAction('.design-item');
	$("input[name='checkall']").click(function(e) {
		if ($(this).is(':checked')) {
			Action.checkAll();
		} else {
			Action.unCheckAll();
		}
	});
	
	var positionArr = new Array();
	var positionId = null;
	$('.position-item').change(function() {
		positionId = $(this).attr('id');
		if ($.inArray(positionId, positionArr) == -1) {
			positionArr.push(positionId);
		}
		if (positionArr.length > 0) {
		
		}
	});
	
	$('#update_position').click(function() {
		if (positionArr.length > 0) {
			Action.updatePosition(positionArr);
		} else {
			alert('Please change position of item(s)!');
		}
	});
	
	$('#delete_item').click(function() {
		var selected = Action.getSelectedItem();
		if (selected.length > 0) {
			if (!confirm('Are you sure?')) {
				return false;
			}
			Action.deleteItem(selected);
		} else {
			alert('Please select item(s)!');
		}
	});
	
	$('#disable_item').click(function() {
		var selected = Action.getSelectedItem();
		if (selected.length > 0) {
			Action.disableItem(selected);
		} else {
			alert('Please select item(s)!');
		}
	});
	
	$('#enable_item').click(function() {
		var selected = Action.getSelectedItem();
		if (selected.length > 0) {
			Action.enableItem(selected);
		} else {
			alert('Please select item(s)!');
		}
	});
	//Create tab function for tabs design
	/* $('#design-manage .nav li').click(function(){
		$('#design-manage .nav li.active').removeClass("active");
		$(this).addClass("active");
		var active_li = $(this).attr("rel");
		$('#design-manage .list_design > tbody > tr:not(.thead)').hide();
		$('.'+active_li).show();
	}); */
	/**Color List**/
	$(".colorlist-item").click(function() {
		var designId = $(this).attr('id').split('_')[1];
		$.ajax({
			type : "POST",
			url : baseUrl + 'pdp/index/getColorList',
			data : {design_id: designId},
			beforeSend : function () {
				$("#loading-mask").attr("style","left: -2px; top: 0px; width: 1034px; height: 833px;");
				$("#loading-mask").show();
			},
			error : function () {
				console.log('Transfer error!');
			},
			success : function (response) {
				if (response != "") {
					var info = $.parseJSON(response);
					if (info.length == 0) {
						alert('Found no item yet! Please add new style for this item.');
						$('#loading-mask').hide();
						return false;
					}
					var row = "";
					var hexcode,
						color_name,
						styleimage,
						filename,
						filenameBack,
						sort,
						optionId;
					
					/**Table header**/
					row += "<tr>";
						row += "<th>Color</td>";
						row += "<th>Color Name</td>";
						row += "<th>Style Img</td>";
						row += "<th>Front Img</td>";
						row += "<th>Back Img</td>";
						//row += "<th>Price</td>";
						row += "<th>Order</td>";
						row += "<th>Action</td>";
					row += "</tr>";
						
					for (var i = 0; i < info.length; i++ ) {
						hexcode = info[i].hexcode;
						styleimage = mediaUrl + info[i].style_image;
						filename = mediaUrl + info[i].filename;
						filenameBack = mediaUrl + info[i].filename_back;
						color_name = info[i].color_name;
						price = info[i].price;
						sort = info[i].sort;
						optionId = info[i].id;
						row += "<tr>";
							row += "<td><span style='background:#"+ hexcode +"' class='color-img-option'>#" + hexcode + "</span></td>";
							row += "<td><input class='span2 color-name-item' type='text' id='colorname_"+ optionId +"' value='"+ color_name +"'></td>";
							if (info[i].style_image == "") {
								row += "<td><span>No Image</span></td>";
							} else {
								row += "<td><img width='50px' alt='No Image' src='"+ styleimage +"'/></td>";
							}
							row += "<td><img width='50px' src='"+ filename +"'/></td>";
							if (info[i].filename_back == "") {
								row += "<td><span>No Image</span></td>";
							} else {
								row += "<td><img width='50px' src='"+ filenameBack +"'/></td>";
							}
							//row += "<td><input class='span1 price-item' type='text' id='price_"+ optionId +"' value='"+ price +"'></td>";
							row += "<td><input class='span1 position-item' type='text' id='sort_"+ optionId +"' value='"+ sort +"'></td>";
							row += "<td><a class='remove-image-item' href='#' id='removeimg_"+ optionId +"'>Remove</a></td>";
						row += "</tr>";
					}
					$("#colorlist_table").html(row);
					$('#loading-mask').hide();
					//Validate input number
					$('.price-item, .position-item').ForceNumericOnly();
					$("#colorlist_modal").modal('show');
				} else {
					alert('There are some errors!');
					location.reload();
				}
			}
		});
		
	});
	$("#colorlist_table").on('click', '.remove-image-item', function() {
		if (!confirm("Are you sure")) {
			return false;
		}
		var id = $(this).attr('id').split('_')[1];
		$.ajax({
			type : "POST",
			url : baseUrl + 'pdp/index/deleteDesignColor',
			data : {design_id: id},
			beforeSend : function () {
				$("#loading-mask").attr("style","left: -2px; top: 0px; width: 1034px; height: 833px; z-index: 10000;");
				$("#loading-mask").show();
			},
			error : function () {
				console.log('Transfer error!');
			},
			success : function (response) {
				if (response == "") {
					$("#removeimg_" + id).parent().parent().remove();
					$("#loading-mask").hide();
				} else {
					alert('There are some errors!');
					location.reload();
				}
			}
		});
	});
/* 	$("#update_color_position").click(function() {
		var newOrder = new Array();
		var optionId, sort;
		$(".position-item").each(function() {
			optionId = $(this).attr('id').split('_')[1];
			sort = $("#sort_" + optionId).val();
			newOrder.push(optionId + '_' + sort);
		});
		$.ajax({
			type : "POST",
			url : baseUrl + 'pdp/index/updateDesignColorPosition',
			data : {position: newOrder.join(',')},
			beforeSend : function () {
				$("#loading-mask").attr("style","left: -2px; top: 0px; width: 1034px; height: 833px; z-index: 10000;");
				$("#loading-mask").show();
			},
			error : function () {
				console.log('Transfer error!');
			},
			success : function (response) {
				if (response == "") {
					$("#loading-mask").hide();
					location.reload();	
				} else {
					alert('There are some errors!');
					location.reload();
				}
			}
		});
	});
	$("#update_color_price").click(function() {
		var newPrice = new Array();
		var optionId, price;
		$(".price-item").each(function() {
			optionId = $(this).attr('id').split('_')[1];
			price = $("#price_" + optionId).val();
			newPrice.push(optionId + '_' + price);
		});
		$.ajax({
			type : "POST",
			url : baseUrl + 'pdp/index/updateDesignColorPrice',
			data : {price: newPrice.join(',')},
			beforeSend : function () {
				$("#loading-mask").attr("style","left: -2px; top: 0px; width: 1034px; height: 833px; z-index: 10000;");
				$("#loading-mask").show();
			},
			error : function () {
				console.log('Transfer error!');
			},
			success : function (response) {
				if (response == "") {
					$("#loading-mask").hide();
					location.reload();	
				} else {
					alert('There are some errors!');
					location.reload();
				}
			}
		});
	});
	$("#update_color_name").click(function() {
		var newName = new Array();
		var optionId, name;
		$(".color-name-item").each(function() {
			optionId = $(this).attr('id').split('_')[1];
			name = $("#colorname_" + optionId).val();
			newName.push(optionId + '_' + name);
		});
		$.ajax({
			type : "POST",
			url : baseUrl + 'pdp/index/updateDesignColorName',
			data : {color_name: newName.join(',')},
			beforeSend : function () {
				$("#loading-mask").attr("style","left: -2px; top: 0px; width: 1034px; height: 833px; z-index: 10000;");
				$("#loading-mask").show();
			},
			error : function () {
				console.log('Transfer error!');
			},
			success : function (response) {
				if (response == "") {
					$("#loading-mask").hide();
					location.reload();	
				} else {
					alert('There are some errors!');
					location.reload();
				}
			}
		});
	}); */
	
	$("#save_design_style").click(function() {
		var newName = new Array(),
			newOrder = new Array(),
			newPrice = new Array(),
			optionId, name, price, sort;

		$(".position-item").each(function() {
			optionId = $(this).attr('id').split('_')[1];
			sort = $("#sort_" + optionId).val();
			newOrder.push(optionId + '_' + sort);
		});
		$(".color-name-item").each(function() {
			optionId = $(this).attr('id').split('_')[1];
			name = $("#colorname_" + optionId).val();
			newName.push(optionId + '_' + name);
		});
		$(".price-item").each(function() {
			optionId = $(this).attr('id').split('_')[1];
			price = $("#price_" + optionId).val();
			newPrice.push(optionId + '_' + price);
		});
		$.ajax({
			type : "POST",
			url : baseUrl + 'pdp/index/updateDesignStyle',
			data : {color_name: newName.join(','), price : newPrice.join(','), position : newOrder.join(',')},
			beforeSend : function () {
				$("#loading-mask").attr("style","left: -2px; top: 0px; width: 1034px; height: 833px; z-index: 10000;");
				$("#loading-mask").show();
			},
			error : function () {
				console.log('Transfer error!');
			},
			success : function (response) {
				if (response == "") {
					$("#loading-mask").hide();
					location.reload();	
				} else {
					alert('There are some errors!');
					location.reload();
				}
			}
		});
	});
	
});