var mstPDPPopup = jQuery.noConflict();
mstPDPPopup(function($){
	var productId = $("#current_product_id").val(),
		setupAreaUrl = $("#request_url").val(),
		windowWidth = $(window).width(),
		baseUrl = $("#base_url").val(),
		popupWidth = (windowWidth * 90) / 100;
		//console.log(popupWidth);
	PDPPopup = {
		openPopup : function(url, title, eventHandler) {
			/* if ($('browser_window') && typeof(Windows) != 'undefined') {
				Windows.focus('browser_window');
				return;
			} */
			var dialogWindow = Dialog.info(null, {
				closable:true,
				resizable:true,
				draggable:true,
				className:'magento',
				windowClassName:'popup-window',
				title: title,
				top:20,
				width:popupWidth,
				height:754,
				zIndex:1000,
				recenterAuto:false,
				hideEffect:Element.hide,
				showEffect:Element.show,
				id:'browser_window',
				url:url,
				onClose:function (param, el) {
					eventHandler();
				}
			});
		},
		closePopup : function() {
			Windows.close('browser_window');
		},
		setupAreaHandler : function() {
			$("#product_info_tabs_pdpdesign").click();
		},
		addDesignHandler : function() {
			console.log("Design Handler");
		},
		setupArea : function() {
			var id,
				url = setupAreaUrl + "productid/" + productId;
			$(".design-area-table").on("click", ".setup-btn", function() {
				id = $(this).attr('id').split('_')[2];
				PDPPopup.openPopup(url + "/areaid/" + id, "Add New Side", PDPPopup.setupAreaHandler );
			});
		}(),
		createSample : function () {
			var	url = baseUrl + "pdp/view/getDesignPage/product-id/" + productId + "/area/backend/key/46783db73cb9894e0ed77647840ef5b5";
			$(".create-sample-btn").on("click", function() {
				PDPPopup.openPopup(url, "Create Pattern Style", PDPPopup.setupAreaHandler );
			});
		}(),
		addDesignColor : function() {
			var	url = $("#add_design_color_url").val() + "productid/" + productId; 
			$(".addcolor-btn").on("click", function() {
				PDPPopup.openPopup(url, "Add Design Color", PDPPopup.setupAreaHandler );
			});
		}(),
		viewDesignColor : function() {
			var	url = $("#view_design_color_url").val() + "productid/" + productId; 
			$(".viewcolor-btn").on("click", function() {
				PDPPopup.openPopup(url, "Design Colors", PDPPopup.setupAreaHandler );
			});
		}(),
        viewTemplates : function () {
            var	url = baseUrl + "pdp/view/viewsample/productid/" + productId;
            $(".view-design-btn").on("click", function() {
                PDPPopup.openPopup(url, "View Sample Design", function(){} );
            });
        }(),
		submitColorForm: function() {
			$("#save_color_form").click(function() {
				$("#add_design_color_form").submit();
			});
		}(),
		deleteSide : function() {
			$(".side_delete_button").on("click", function () {
				if (!confirm("Are you sure?")) {
					return false;
				}
				PdpSide.sendRequest($(this).attr('rel'), function(data) {
					if(data == ""){
						$("#product_info_tabs_pdpdesign").click();
					}else{
						alert("Something went wrong. Can not delete this side!");
					}
				});
			});
		}(),
		sendRequest : function (url, callback) {
        	$.ajax({
				type : "GET",
				url : url,
				beforeSend : function () {
					$("#loading-mask").attr("style","left: -2px; top: 0px; width: 1034px; height: 833px; z-index: 100000;");
					$("#loading-mask").show();
				},
				error : function () {
					console.log('Transfer error!');
				},
				success : function (response) {
					callback(response);
				}
			});
        }, 
		editSide : function () {
			$(".edit_side").on("click", function () {
				PDPPopup.openPopup($(this).attr('rel'), $(this).attr('title'), PDPPopup.setupAreaHandler );
			});
		}(),
		pdpInlineInput : function() {
			var baseUrl, sideId, updateFieldName, updateVal, request;
			$(".pdp-inline-input").on("change", function() {
				var inputId = $(this).attr("id").split('_'),
					updateVal = $(this).val(),
					sideId = inputId[2],
					updateFieldName = inputId[1],
					baseUrl = $("#inline_input_url").val(),
					request = baseUrl + "side_id/" + sideId + "/update-info/" + updateFieldName + "-" + updateVal + "?isAjax=true";
				PdpSide.sendRequest(request, function (response) {
					if (response != "") {
						alert("Soemthing went wrong! Can not update side info yet!");
					} else {
						$("#product_info_tabs_pdpdesign").click();
					}
				});
			});
		}(),
        previewImage : function(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader(),
                	sideId, imgSelector;
                reader.onload = function (e) {
                	sideId = input.name.split('_')[2];
                	imgSelector = $("#preview-img-" + sideId);
                	imgSelector.attr('src', e.target.result).load(function(){
                		$(input.target).removeClass('validation-failed');
                		imgSelector.show();
                    });
                }
                reader.readAsDataURL(input.files[0]);
            }
        }, 
        previewOverlayImage : function(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader(),
                	sideId, imgSelector;
                reader.onload = function (e) {
                	sideId = input.name.split('_')[2];
                	imgSelector = $("#preview-overlay-img-" + sideId);
                	imgSelector.attr('src', e.target.result).load(function(){
                		$(input.target).removeClass('validation-failed');
                		imgSelector.show();
                    });
                }
                reader.readAsDataURL(input.files[0]);
            }
        },
        previewThumbnailImage : function(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                	sideId = input.name.split('_')[2];
                	imgSelector = $("#preview-color-thumbnail");
                	imgSelector.attr('src', e.target.result).load(function(){
                		imgSelector.show();
                    });
                }
                reader.readAsDataURL(input.files[0]);
            }
        },
        deleteProductColor : function() {
        	var deleteUrl = $("#delete_product_color_url").val(),
        		productColorId;
        	$('.delete-product-color').on("click", function(e) {
        		if (!confirm("Are you sure?")) {
        			return false;
        		}
        		productColorId = $(this).attr("id").split("_")[1];
        		PDPPopup.sendRequest(deleteUrl + 'delete/' + productColorId, function(response) {
        			if (response == "") {
        				$(e.target).parent().parent().remove();
        				$("#loading-mask").hide();
        			} else {
        				alert("Can not delete this item!");
        			}
        		});
        	});
        }(),
		addNewDesignColor : function() {
			var productId = $("#current_product_id").val(),
				url = $("#add_design_color_url").val() + "productid/" + productId
			$(".add-new-design-color").on("click", function() {
				window.location.href = url;
			});
		}(),
		viewDesignItemInOrder : function() {
			var baseUrl = $("#mst_base_url").val(),
			itemId, 
			orderId,
			productId,
			url;
			$(".item-container").on("click", '.pdp-order-item', function(e) {
				e.preventDefault();
				e.stopPropagation();
				productId = $(this).attr('productid');
				itemId = $(this).attr('itemid');
				orderId = $(this).attr('orderid');
				url = baseUrl + "pdp/view/finalDesign/product-id/"+ productId + "/order-id/" + orderId + "/item-id/" + itemId;
				PDPPopup.openPopup(url, "View Design", function() {});
			});
		}(),
		reOrderAdditionalInfo : function() {
			var itemId, customizeLink;
			$(".view-customize-design").hide();
			$(".order-tables .item-container").each(function() {
				itemId = $(this).attr('id').split('_')[2];
				customizeLink = $("#customize-" + itemId).html();
				$(this).find(".item-options").prepend(customizeLink);
				
			});
		}()
	}
});