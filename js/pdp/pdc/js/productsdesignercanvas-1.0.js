;(function(global, $) {
    // 'new' an object
	var PDC = function() {
		return new PDC.init();
	}
    // hidden within the scope of the IIFE and never directly accessible
    var config = {
        base_url: $("#base_url").val(),
        media_url: $("#pdp_media_url").val(),
        save_thumbnail_url: $("#base_url").val() + "pdp/index/saveBase64Image/",
        save_json_url: $("#base_url").val() + "pdp/index/saveJsonfile/",
        save_admin_template: $("#base_url").val() + "pdp/index/saveAdminTemplate/",
        image_options: {
            format: 'jpeg',
            quality: $("#image_quality").val() || 0.7
        }, 
        show_log: true,
        // Default image if thumbnail not render successfully
        default_thumbnail: $("#pdp_media_url").val() + "pdp/images/default_thumbnail.jpg"
    }
    // prototype holds methods (to save memory space)
    // All core functions place here
	PDC.prototype = {
		version: '1.0.0',
        //All side info place here
        sides: {},
        // Status 0 || 1 (1 mean all ajax done, thumbnail saved) 
        status: 0,
        // Actions: SWITCH_SIDE, SAVE_SAMPLE
        action: '',
        sideIndexBeforeSwitch: 0,
        // Target of current action
        target: {},
        //Newest json filename return from saveJsonFile
        newestJsonFilename : '',
        //Log process
        showLog: function(message, type) {
            //Show log
            if(config.show_log) {
                type = type || 'log';
                console[type](message);
            }
        },
        //Flag first load
        firstLoadFlag: true,
        //Accept json exported from fabricjs canvas, send base64 image to server and return image path
        renderThumbnail: function(json, options, callback) {
            var self = this;
            json = json || this.getCurrentCanvas().toJSON();
            if(!json) return;
            //Current Active canvas
            var baseCanvas = this.getCurrentCanvas();
            if(baseCanvas === undefined) return;
            //New canvas
            var newCanvas = new fabric.Canvas();
            newCanvas.setWidth(baseCanvas.getWidth());
            newCanvas.setHeight(baseCanvas.getHeight());
            newCanvas.loadFromJSON(json, function() {
                var basecodeString;
                /**
                //Some options example
                //Generate jpeg dataURL with lower quality
                var dataURL = canvas.toDataURL({
                  format: 'jpeg',
                  quality: 0.8
                });
                //Generate cropped png dataURL (clipping of canvas)
                var dataURL = canvas.toDataURL({
                  format: 'png',
                  left: 100,
                  top: 100,
                  width: 200,
                  height: 200
                });
                //Generate double scaled png dataURL
                var dataURL = canvas.toDataURL({
                  format: 'png',
                  multiplier: 2
                });
                **/
                options = options || config.image_options;
                basecodeString = newCanvas.toDataURL(options);
                self.doRequest(options.url || config.save_thumbnail_url, {
                    base_code_image : basecodeString,
                    format: options.format || config.image_options.format,
                    options: options
                }, callback);
            });
        },
        getCurrentCanvas: function() {
            var self = this;
            if(global.canvas instanceof fabric.Canvas) {
                return global.canvas;
            }
            return this.canvas;
        },
        //For access to external canvas, something like from export panel
        setCurrentCanvas: function(fabricCanvas) {
            var self = this;
            if(fabricCanvas instanceof fabric.Canvas) {
                self.canvas = fabricCanvas;
            }
            return this;
        },
        doRequest: function (url, data, callback) {
			$.ajax({
				type: "POST",
				url: url,
				data: data,
				beforeSend: function() {
                    //Developer can using this class to make their own process bar
					$('.pdploading').show();
                    if(global.Pace !== undefined) {
                        Pace.restart();
                    }
				},
				error: function() {
					console.log("Something went wrong...");
				}, 
				success: function(response) {
					callback(response);
					$('.pdploading').hide();
				}
			});
		},
        saveSampleDesign: function() {
            var self = this;
            self.showLog("Save sample design", 'info');
            self.saveJsonFile(function(response) {
                self.showLog("Save json file to admin template table", "info");
                var responseJSON = JSON.parse(response);
                if(responseJSON.status == "error") {
                    self.showLog("Can not save json file in saveSampleDesign() function", "warn");
                    return false;
                }
                self.newestJsonFilename = responseJSON.filename;
                //Save file name as an template
                self.doRequest(config.save_admin_template, {
                    product_id: self.getCurrentProductId,
                    pdp_design: responseJSON.filename
                }, function(response) {
                    console.log(response);
                    if (window.top.Windows !== undefined) {
                        window.top.Windows.closeAll();
                    } else {
                        $(".pdploading").hide();
                        //location.reload();
                    }
                });
            });
        },
        saveCustomerDesign: function() {
            var self = this;
            self.showLog("Save Customer Design", "info");
            self.saveJsonFile(function(response) {
                var responseJson = JSON.parse(response);
                self.showLog("Save json to customer account if user request", "info");
                if($("#customer_design_json").length && (responseJson.filename || '')) {
                    $("#customer_design_json").val(responseJson.filename);
                    self.newestJsonFilename = responseJson.filename;
                    self.previewResultImageBeforeSave();
                    $("#savePopup").modal("show");
                }
            });
        },
        previewResultImageBeforeSave: function() {
            var self = this;
            $(".preview-image-before-save").html("");
            $.each(self.sides, function() {
                var liImg = '<li><img src="'+ this.image_result +'"/></li>';
                $(".preview-image-before-save").append(liImg);
            });
            //Scroll bar more beautiful
            $(".preview-image-before-save").mCustomScrollbar({
                scrollButtons:{ enable: true },
                axis:"y",
                theme: 'inset-2-dark',
                live: true
            });
        },
        saveJsonFile: function(callback) {
            if(this.sides) {
                this.doRequest(config.save_json_url, {json_content: JSON.stringify(this.sides)}, callback); 
            }
        },
        saveDesignToCustomerAccount: function() {
            var self = this,
                data = {
                    'product_id': $("#customer_design_product_id").val(),
                    'filename': $("#customer_design_json").val(),
                    'design_title': $("#design_title").val(),
                    'design_note': $("#design_note").val()
                },
                saveDesignUrl = config.base_url + "pdp/customerdesign/saveToCustomerLoggedIn/";
            self.showLog("Design data before save", "info");
            if(data.design_title == "") {
                //Valid design title field
                $("#design_title").closest(".form-group").addClass('has-error');
                $("#design_title").focus();
                return;
            } else {
                //Remove red border
                $("#design_title").closest(".form-group").removeClass('has-error');
            }
            //Send data to server
            self.doRequest(saveDesignUrl, {design_info: data}, function(response) {
                var responseInJson = JSON.parse(response);
                if(responseInJson.status == "success") {
                    $(".for-guest").hide();
                    self.saveAndContinue();
                } else if (responseInJson.status == "guest") {
                    //Hide message first time
                    $(".for-guest").show();
                } else {
                    alert(responseInJson.message);
                    return false;
                }
            });
        },
        saveAndContinue: function() {
            var self = this;
            //Pass data from iframe to parent window
            var mainWindow = top.document;
            self.showLog("Save And Continue", "info");
            //Update json file to extra_options
            self.showLog("Add new json filename to extra_option hidden input. " + self.newestJsonFilename, "info");
            $("input[name='extra_options']", mainWindow).val(self.newestJsonFilename);
            //Update extra_options_value as well
            $("#extra_options_value", mainWindow).val(JSON.stringify(self.sides));
            //Update sample_images hidden field to show preview
            if(global.LoadDesign) {
                var thumbnails = [];
                for(var side in self.sides) {
                    if(self.sides.hasOwnProperty(side)) {
                        thumbnails.push({
                            side_name: self.sides[side].side_name,
                            image_result: self.sides[side].image_result
                        });
                    }
                }
                $("#sample_images", mainWindow).val(JSON.stringify(thumbnails));
                LoadDesign.showSampleImage();
                LoadDesign.reloadPrice();
                self.showLog("Done update thumbnail and reload Price", "info");
            }
            // close popup or save current json in session and reload page
            $("#savePopup").modal("hide");
            $("#close_iframe").click();
            
        },
        //This function might need to update with different magento theme
        previewDesign: function() {
            
        },
        reset: function() {
            this.status = 0;
            this.action = '';
        },
        saveBeforeAction: function(action) {
            var self = this,
                action = action || '';
            self.showLog("Save design before any action", 'info');
            self.status = 0;
            self.saveCurrentSide(action);
            return self;
        },
        switchSide: function() {
            var self = this;
            self.showLog("Switch Side", 'info');
            self.reset();
            return this;
        },
        getActiveSideIndex: function() {
            return this.getActiveSide().index();
        },
        handleCurrentAction: function() {
            var self = this,
                _timeout;
            if(self.status === 0) {
                _timeout = setTimeout(function() {
                    self.handleCurrentAction();
                }, 100);
            } else {
                switch(self.action) {
                    case "SAVE_SAMPLE":
                        self.saveSampleDesign();
                        break;
                    case "SWITCH_SIDE":
                        self.switchSide();
                        break;
                    case "SAVE_CUSTOMER_DESIGN" :
                        self.saveCustomerDesign();
                        break;
                    case "SHARE" :
                        self.readyToShareDesign();
                        break;    
                    case "" :
                        return false;
                        break;
                }
                clearTimeout(_timeout);
            }
        },
        //Save current side, save base64 image to server and return current side object
        saveCurrentSide: function(action) {
            var _activeSide = this.getActiveSide(),
                self = this,
                action = action || "";
            self.showLog("Save current side", "info");
            if(_activeSide !== undefined) {
                self.sideIndexBeforeSwitch = self.getActiveSideIndex();
                self.resetZoom();
                var json = JSON.stringify(self.getCurrentCanvas().toJSON(['name','price','tcolor','isrc','icolor','id'])) || '';
                var final_price = self.getFinalPrice(); 
                var _sideData = {
                    side_id: _activeSide.attr("pdc-side"),
                    side_name: _activeSide.attr("title"),
                    side_img: _activeSide.attr("side_img"),
                    side_overlay: _activeSide.attr("overlay"),
                    side_inlay: _activeSide.attr("inlay"), // hold width and height of background image,
                    side_color_id: $(".pdc_design_color li.active").attr("pdc-color") || "",
                    side_color: _activeSide.attr("side_color") || $(".pdc_design_color li.active").attr("color") || "",
                    image_result: config.default_thumbnail,
                    final_price: final_price,
                    background_type: _activeSide.attr("background_type"),
                    color_code: _activeSide.attr("side_color") || _activeSide.attr("color_code"),
                    json: json
                }
                self.showLog(_sideData);
                self.renderThumbnail(_sideData.json, null, function(response) {
                    var _responseJSON = JSON.parse(response);
                    if(_responseJSON.status === "success") {
                        _sideData.image_result = _responseJSON.thumbnail_path;
                        self.sides[self.sideIndexBeforeSwitch] = _sideData;
                        self.status = 1;
                        self.handleCurrentAction();
                    }    
                });
                return this;
            }
        },
        getFinalPrice: function() {
            this.showLog("Calculate final price for current side", "info");
            var total = 0,
                _objectPrice;
            //Artwork price add only admin enable
            if(this.isEnableClipartPrice()) {
                this.getCurrentCanvas().forEachObject(function(o) {
                    _objectPrice = parseFloat(o.price || 0);
                    total += _objectPrice;
                });
            }
            //Side price if has customized object
            if(this.getCurrentCanvas().getObjects().length) {
                var sidePrice = parseFloat(this.getActiveSide().attr("price"));
                total += sidePrice;
            }
            return total.toFixed(2);
        },
        isEnableClipartPrice: function() {
            var _isEnable = false;
            if($("#pdc_product_config").length) {
                var config = JSON.parse($("#pdc_product_config").val());
                if(config.show_price === '1') {
                    _isEnable = true;
                } 
            }
            return _isEnable;
        },
        getActiveSide: function() {
            return $(".pdp_side_item_content.active");
        },
        getCurrentProductId: function() {
            return $("#current_product_id").val();
        },
        //init sample data, share, ...
        initSidesData: function() {
            var _currentJson = this.getSampleJson();
            if(_currentJson) {
                this.showLog("Assign json to sides properties!", "info");
                //this.showLog(_currentJson);
                this.sides = _currentJson;
                this.setActiveSideColor();
            }
        },
        getSampleJson: function() {
            var self = this;
            //Should get json in parent window, high priority
            var mainWindow = top.document;
            self.showLog("Get Sample JSON if exists!", "info");
            if($("#extra_options_value", mainWindow).length && $("#extra_options_value", mainWindow).val() !== "") {
                try {
                    var _designInJson = JSON.parse($("#extra_options_value", mainWindow).val());
                    if(_designInJson) return _designInJson;
                } catch(e) {
                    this.showLog(e, "error");
                }
            } else {
                //Load sample design in backend iframe, load from iframe
                if($("#extra_options_value").length && $("#extra_options_value").val() !== "") {
                    try {
                        var _designInJson = JSON.parse($("#extra_options_value").val());
                        if(_designInJson) return _designInJson;
                    } catch(e) {
                        this.showLog(e, "error");
                    }
                }
            }
        },
        resetZoom: function() {
            var self = this,
                _canvas = self.getCurrentCanvas();
            _canvas.setZoom(1);
            if(self.sides[self.getActiveSideIndex()] === undefined) {
                var originalSize = self.getActiveSide().attr("inlay").split(",");
            } else {
                var originalSize = self.sides[self.getActiveSideIndex()].side_inlay.split(",");
            }
            _canvas.setWidth(originalSize[0]);
            _canvas.setHeight(originalSize[1]);
            //Reset the size of wrapper as well
            if($('#wrap_inlay').length) {
                $('#wrap_inlay').css({
                    'width': originalSize[0], 
                    'height': originalSize[1]
                });
            }
            return _canvas;
        },
        downloadPng: function() {
            var self = this;
            var json = self.resetZoom().toJSON();
            self.renderThumbnail(json, {
                format: 'png', 
                multiplier: 1,
                url: config.base_url + 'pdp/index/saveBase64ImageExport/',
                is_backend: $("#is_backend").length
            }, 
            function(response) {
                self.showLog("The png file response to download png event", "info");
                var responseJson = JSON.parse(response);
                if(responseJson.status === "success") {
                    window.location = responseJson.thumbnail_path;
                    $("#downloadPopup").modal("hide");
                    return false;
                }
                alert(responseJson.message);
            });
        },
        downloadPdfFromPng: function() {
            var self = this,
                canvas = self.resetZoom(),
                canvasBase64Code = canvas.toDataURL(),
                saveUrl = config.base_url + 'pdp/index/createPdfFromPng';
            self.doRequest(saveUrl, {
                png_string: canvasBase64Code,
            }, 
            function(response) {
                self.showLog("The pdf file response to download pdf event", "info");
                var responseJson = JSON.parse(response);
                if(responseJson.status === "success") {
                    window.location = responseJson.pdf_url;
                    $("#downloadPopup").modal("hide");
                    return false;
                }
                alert(responseJson.message);
            });
        },
        readyToShareDesign: function() {
            var self = this,
                productUrl = $("#product_url").val();
            self.showLog("Prepare add this share link", "info");
            self.saveJsonFile(function(response) {
                self.showLog("Save json before share action", "info");
                var responseInJson = JSON.parse(response);
                if(responseInJson.status == "success") {
                    //Show share thumbnail
                    $(".share-thumbnails").html("");
                    $.each(self.sides, function() {
                        var listHtml = "<li><img src='"+ this.image_result +"' width='200px' alt='"+ this.side_name +"' /></li>";
                        $(".share-thumbnails").append(listHtml);
                    });
                    //Update share url
					try {
						var newUrl =  productUrl + "?share=" + responseInJson.id;
						if(global.addthis !== undefined) {
							// Method 1
							for(var i = 0; i < global.addthis.links.length; i++){
								global.addthis.links[i].share.url = newUrl;
								global.addthis.links[i].share.title = "Design Your Own";
							}
							//Method 2
							/* addthis.update('share', 'url', newUrl); 
							addthis.url = newUrl;                
							addthis.toolbox(".addthis_toolbox"); */
							$("#sharingPopup").modal("show");
							return;
						}
					} catch (error) {
						
					}                    
                } else {
                    alert(responseInJson.message);
                }
            });
        },
        setActiveSideColor: function() {
            var _activeColor = this.getActiveSide().attr("color_code") || "";
            this.showLog("Active color: " + _activeColor, "info");
            if(_activeColor !== "") {
                $("#colorTab .pdc_design_color li").each(function() {
                    if($(this).attr("color") === _activeColor) {
                        $(this).addClass("selected");
                        return false;
                    }
                });
            }
            
        },
        //when move from one server to another, some image fixed path in json sample
		updateImagePathBeforeAdd: function(object) {
			if(object.type == "image") {
				var pdcMediaUrl = config.media_url,
					oldSrc = object.src;
				if(oldSrc !== "" && oldSrc !== undefined) {
					if(!oldSrc.match(pdcMediaUrl)) {
						object.src = object.isrc = this.replaceImagePath(oldSrc);
					}
				}
			}
			return object;
		},
		replaceImagePath: function(oldSrc) {
			var pdcMediaUrl = config.media_url;
			if(oldSrc !== "" && oldSrc !== undefined) {
				if(!oldSrc.match(pdcMediaUrl)) {
					this.showLog("Replace image path", "info");
					//http://mageboat.com/demopdc/media/pdp/images/artworks/artwork1431746883.png
					//http://productsdesignercanvas.com/demo/media/pdp/images/
					//Replace path here
					var temp = oldSrc.split("/pdp/images/"),
						newUrl = pdcMediaUrl + temp[1];
						return newUrl;
				}
			}
			//Same server will return oldSrc;
			return oldSrc;
		},
		//When render canvas in export panel
		checkImagePathInJson: function(json) {
			var self = this,
				jsonDecode = JSON.parse(json),
				backgroundImage = jsonDecode.backgroundImage,
				overlayImage = jsonDecode.overlayImage,
				objects = jsonDecode.objects;
			//Update canvas background path
			if(backgroundImage !== undefined && backgroundImage.type == "image") {
				backgroundImage.src = this.replaceImagePath(backgroundImage.src);
			}
			//Update canvas overlay image path
			if(overlayImage !== undefined && overlayImage.type == "image") {
				overlayImage.src = this.replaceImagePath(overlayImage.src);
			}
			// Update all object that type = image
			objects.forEach(function(o) {
				if(o.type == "image") {
					self.updateImagePathBeforeAdd(o);
				}
            });
			return JSON.stringify(jsonDecode);
		},
        removeSampleData: function() {
            if(!confirm("Are you sure?")) return false;
            var removeSampleUrl = config.base_url + "pdp/index/removeSampleData/product-id/" + this.getCurrentProductId();
            this.doRequest(removeSampleUrl, {}, function(response) {
                var responseJson = JSON.parse(response);
                if(responseJson.status === "success") {
                    window.location.reload();
                } else {
                    alert(responseJson.message);
                    return false;
                }
            });
        }
	}
	// the actual object is create here, allowing us to 'new' an object without calling new
    PDC.init = function() {
		var self = this;
        // Add event listener
        $("[pdc-data='pdc-btn']").click(function() {
            self.action = $(this).attr("pdc-action") || '';
            self.target = $(this);
            //Donothing if click to current side
            if(self.action == "SWITCH_SIDE" && self.target.hasClass("active")) return;
            if(self.action == "DOWNLOAD") {
                $("#downloadPopup").modal("show");
                return;
            };
            if(self.action == "DOWNLOAD_AS_IMAGE") {
                self.downloadPng();
                return;
            };
            if(self.action == "DOWNLOAD_AS_PDF") {
                self.downloadPdfFromPng();
                return;
            };
            if(self.action == "SHARE") {
                //Make sure add this ready in global
                if(global.addthis !== undefined) {
                    self.saveBeforeAction();    
                }
                return;
            };
            self.saveBeforeAction(self.action);
        });
        $("[pdc-data='pdc-save-continue']").click(function() {
            self.saveAndContinue();
        });
        //Customer Logged In. Save design to customer account via ajax and close popup
        $("[pdc-data='pdc-saveto-account']").click(function() {
            self.saveDesignToCustomerAccount();
        });
        //Remove sample data
        $('[pdc-data="pdc-remove-sample"]').click(function() {
            self.removeSampleData();
        });
        //init sides data, assign json to sides properties
        self.initSidesData();
	}
	PDC.init.prototype = PDC.prototype;
    global.PDC = PDC;
}(window, jQuery));
