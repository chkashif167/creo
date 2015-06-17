var action = jQuery.noConflict();
action(function($) {
	var baseUrl = $("#base_url").val();
	PDP = {
		checkboxSelector : ".checkbox-item",
		checkboxFontSelector : ".font-checkbox",
		init : function () {
			/**Init all action here**/
			PDP.selectItem();
			PDP.selectAllItem();
			PDP.deleteImageItem();
			PDP.removeAllChecked();
            PDP.deleteShapeItem();
		},
		initFont : function () {
			PDP.selectAllFont();
			PDP.deleteFontItem();
			PDP.removeAllFontChecked();
		},
		selectItem : function() {
			$("#select_item").click(function() {
				PDP.enableSelectItem(PDP.checkboxSelector);
			});
		},
		removeAllChecked : function() {
			$("#uncheck_all_item").click(function() {
				PDP.unCheckAllItem(PDP.checkboxSelector);
			});
		},
		selectAllItem : function() {
			$("#select_all_item").click(function() {
				PDP.enableSelectItem(PDP.checkboxSelector);
				PDP.checkAllItem(PDP.checkboxSelector);
			});
		},
		selectAllFont : function() {
			$("#select_all_item").click(function() {
				PDP.checkAllItem(PDP.checkboxFontSelector);
			});
		},
		removeAllFontChecked : function() {
			$("#uncheck_all_item").click(function() {
				PDP.unCheckAllItem(PDP.checkboxFontSelector);
			});
		},
		enableSelectItem : function(selector) {
			$("#container " + selector).each(function() {
				$(this).show();
			});
		},
		checkAllItem : function (selector) {
			$(selector).each(function() {
				$(this).prop('checked', true);
			});
		},
		unCheckAllItem : function (selector) {
			$(selector).each(function() {
				$(this).prop('checked', false);
			});
		},
		deleteImageItem : function () {
			$("#delete_selected_item").click(function() {
				var selectedItem = PDP.getSelectedItem(PDP.checkboxSelector);
				if (selectedItem.length == 0) {
					alert('Please select item(s)!');
				} else {
					if (confirm("Are you sure?")) {
						$.ajax({
							type : "POST",
							url : baseUrl + "pdp/index/deleteImageById",
							data : {img_list : selectedItem.join(',')},
							beforeSend : function() {
								$("#loading-mask").attr("style","left: -2px; top: 0px; width: 1034px; height: 833px; z-index: 10000;");
								$("#loading-mask").show();
							}, 
							error : function() {
								console.log('Transfer error!');
							},
							success : function(response) {
								if (response == "") {
									//location.reload();
									for (var i = 0; i < selectedItem.length; i++) {
										$("#" + selectedItem[i]).parent().remove();
									}
									ImgItem.pagingCollection('category_filter', 'pdp/index/getImagePaging');
									$("#loading-mask").hide();
								} else {
									alert(response);
									location.reload();
								}
							}
						});
					}
				}
			});
		},
        deleteShapeItem : function () {
			$("#delete_selected_shape").click(function() {
				var selectedItem = PDP.getSelectedItem(PDP.checkboxSelector);
				if (selectedItem.length == 0) {
					alert('Please select item(s)!');
				} else {
					if (confirm("Are you sure?")) {
						$.ajax({
							type : "POST",
							url : baseUrl + "pdp/shape/deleteImageById",
							data : {img_list : selectedItem.join(',')},
							beforeSend : function() {
								$("#loading-mask").attr("style","left: -2px; top: 0px; width: 1034px; height: 833px; z-index: 10000;");
								$("#loading-mask").show();
							}, 
							error : function() {
								console.log('Transfer error!');
							},
							success : function(response) {
								if (response == "") {
									//location.reload();
									for (var i = 0; i < selectedItem.length; i++) {
										$("#" + selectedItem[i]).parent().remove();
									}
									ShapeItem.pagingCollection('category_filter', 'pdp/shape/getImagePaging');
									$("#loading-mask").hide();
								} else {
									alert(response);
									location.reload();
								}
							}
						});
					}
				}
			});
		},
		deleteFontItem : function () {
			$("#delete_selected_item").click(function() {
				var selectedItem = PDP.getSelectedItem(PDP.checkboxFontSelector);
				if (selectedItem.length == 0) {
					alert('Please select item(s)!');
				} else {
					if (confirm("Are you sure?")) {
						$.ajax({
							type : "POST",
							url : baseUrl + "pdp/index/deleteFontById",
							data : {font_list : selectedItem.join(',')},
							beforeSend : function() {
								$("#loading-mask").attr("style","left: -2px; top: 0px; width: 1034px; height: 833px; z-index: 10000;");
								$("#loading-mask").show();
							}, 
							error : function() {
								console.log('Transfer error!');
							},
							success : function(response) {
								if (response == "") {
									location.reload();
									$("#loading-mask").hide();
								} else {
									alert(response);
									location.reload();
								}
							}
						});
					}
				}
			});
		},
		getSelectedItem : function (selector) {
			var selected = new Array();
			$(selector).each(function() {
				if ($(this).is(':checked')) {
					selected.push($(this).attr('id'));
				}
			});
			return selected;
		}
	}
});