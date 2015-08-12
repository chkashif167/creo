var shape = jQuery.noConflict();
shape(function($){
	/**Image Action**/
	PDP.init();
	var baseUrl = $("#base_url").val();
	var mediaUrl = $("#media_url").val();
	ShapeItem = {
		pagingCollection : function(id, action_link){
			/** Selector*/
			var current_page_selector = $('input[name="current_page"]');
			var page_size_selector = $('input[name="page_size"]');
			var category_selector = $('select[name="category_filter"]');
			/** End declare selector*/
			var current_page = current_page_selector.val();
			var category = category_selector.val();
			var page_size,
				page,
				view_per_page;
			switch(id){
				case 'next_page_btn':
					current_page ++;
					current_page_selector.val(current_page);
					break;
				case 'previous_page_btn':
					current_page --;
					current_page_selector.val(current_page);
					break;
				case 'view_per_page':
					view_per_page = $('#'+id).val();
					page_size_selector.val(view_per_page);
					current_page_selector.val(1);
					break;
				case 'category_filter':
					view_per_page = $('#view_per_page').val();
					page_size_selector.val(view_per_page);
					current_page_selector.val(1);
					break;	
					
			}
			//Get page_size and curent page
			page_size = page_size_selector.val();
			current_page = current_page_selector.val();
			//Request data from server
			var url= baseUrl + action_link;
			var adminhtmlKey = $("#secret_key").val().split('/key/')[1];
			$.ajax({
				type:"POST",
				url: url,
				data: {current_page : current_page, page_size : page_size, url : action_link, category : category},
				beforeSend:function(){
					$("#loading-mask").attr("style","left: -2px; top: 0px; width: 1034px; height: 833px;");
					$("#loading-mask").show();
				},
				success:function(data){
					if(data){
						var paging_collection = $.parseJSON(data);
						var paging_text = paging_collection.paging_text;
						var collection = paging_collection.collection;
						
						var imageItems = "",
							newColorUrl;
						if (collection != undefined) {
							for (var i = 0; i < collection.length; i++) {
								for (var j = 0; j < collection[i].length; j++) {
                                    
                                    imageItems += '<div class="col-sm-4 col-md-2">';
                                        imageItems += '<div class="thumbnail">';
                                            imageItems += '<input type="checkbox" style="display: none;" id="img_'+ collection[i][j].id +'" class="checkbox-item"/>';
                                            imageItems += '<img src="'+ mediaUrl + collection[i][j].filename +'" alt="" width="100px" />';
                                            imageItems += '<div class="caption">';
                                                imageItems += '<div class="form-group">';
                                                    imageItems += '<label for="shape_'+ collection[i][j].id +'">Shape Name</label>';
                                                    imageItems += '<input type="text" class="form-control shape-name" value="'+ collection[i][j].original_filename.replace(".svg", "") +'" id="shape_'+ collection[i][j].id +'">';
                                                imageItems += '</div>';
                                                imageItems += '<div class="form-group">';
                                                    imageItems += '<label for="shapetag_'+ collection[i][j].id +'">Shape Name</label>';
                                                    imageItems += '<input type="text" class="form-control shape-name" value="'+ collection[i][j].tag +'" id="shapetag_'+ collection[i][j].id +'">';
                                                imageItems += '</div>';
                                            imageItems += '</div>';    
                                        imageItems += '</div>';
                                    imageItems += '</div>';
								}
							}
						} else {
							imageItems = '<div class="no-item"> No Items Found</div>';
						}
						//Add paging_text to div
						$('.paging-area').html(paging_text);
						$('#container .row').html(imageItems);
						$("#loading-mask").hide();
					}else{
						alert(data);
						window.location.reload();
					}
				}
			});
		},
        changeShapeNameEvent: function() {
            $("#shape-manage").on("keypress", ".shape-name", function(e) {
                if(e.keyCode === 13) {
                    if($(this).val() !== "") {
                        ShapeItem.changeShapeName($(this).attr("id").split('_')[1], $(this).val());
                    } else {
                        $(this).focus();
                    }
                }
            });
            $("#shape-manage").on("change", ".shape-name", function(e) {
                if($(this).val() !== "") {
                    ShapeItem.changeShapeName($(this).attr("id").split('_')[1], $(this).val());
                } else {
                    $(this).focus();
                }
            });
        }(),
        changeShapeName: function(shapeId, shapeName) {
            var self = this;
            if(shapeId && shapeName) {
                var changeShapeNameUrl = baseUrl + "pdp/shape/changeShapeName",
                    data = {
                        shape_id: shapeId,
                        original_filename: shapeName
                    };
                self.doRequest(changeShapeNameUrl, data, function(response) {
                    var responseJSON = JSON.parse(response);
                    if(responseJSON.status == 'error') {
                        alert(responseJSON.message);
                    }
                });
            }
        },
        changeShapeTagEvent: function() {
            $("#shape-manage").on("keypress", ".shape-tag", function(e) {
                if(e.keyCode === 13) {
                    if($(this).val() !== "") {
                        ShapeItem.changeShapeTag($(this).attr("id").split('_')[1], $(this).val());
                    } else {
                        $(this).focus();
                    }
                }
            });
            $("#shape-manage").on("change", ".shape-tag", function(e) {
                if($(this).val() !== "") {
                    ShapeItem.changeShapeTag($(this).attr("id").split('_')[1], $(this).val());
                } else {
                    $(this).focus();
                }
            });
        }(),
        changeShapeTag: function(shapeId, tag) {
            var self = this;
            if(shapeId && tag) {
                var changeShapeTagUrl = baseUrl + "pdp/shape/changeShapeTag",
                    data = {
                        shape_id: shapeId,
                        tag: tag
                    };
                self.doRequest(changeShapeTagUrl, data, function(response) {
                    var responseJSON = JSON.parse(response);
                    if(responseJSON.status == 'error') {
                        alert(responseJSON.message);
                    }
                });
            }
        },
        doRequest: function (url, data, callback) {
			$.ajax({
				type: "POST",
				url: url,
				data: data,
				beforeSend: function() {
                    //Developer can using this class to make their own process bar
					$('#loading-mask').show();
				},
				error: function() {
					console.log("Something went wrong...");
				}, 
				success: function(response) {
					callback(response);
                    $('#loading-mask').hide();
				}
			});
		}
	}
});