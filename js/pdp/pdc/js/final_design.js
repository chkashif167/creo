var mst = jQuery.noConflict();
mst(document).ready(function($) {
    var pdc = PDC();
	var allCanvas = {},
        baseUrl = $("#base_url").val(),
        pdcMediaUrl = $("#pdp_media_url").val(),
		defaultRenderTime = 1000;
	PDCExport = {
        //Canvas background before exclude
        canvasOriginalBackground: {},
        canvasOriginalOverlay: null,
		init : function() {
			PDCExport.renderToCanvas();
			setTimeout(function() {
				$.each(allCanvas, function() {
					this.renderAll();
					
				});
			}, defaultRenderTime);
			//this.addControls();
		},
        getDesignJson : function() {
            var jsonContent = $("#final_design_json").val();
            return JSON.parse(jsonContent);
        },
		renderToCanvas : function() {
            pdc.showLog("Load From Json", "info");
            var self = this,
                sides = self.getDesignJson();
            $.each(sides, function() {
                var sideInfo = this;
                var canvasId = "canvas_side_" + sideInfo.side_id;
                allCanvas[canvasId] = new fabric.Canvas(canvasId);
                var _validJson = pdc.checkImagePathInJson(sideInfo.json);
                allCanvas[canvasId].loadFromJSON(_validJson, allCanvas[canvasId].renderAll.bind(allCanvas[canvasId]), function(o, object) {
                    object.set({
                        selectable: false
                    });
                    //Remove shadow if not setting, prevent duplicate text while export svg
                    if (object.shadow !== null && object.shadow !== "") {
                        if (parseInt(object.shadow.toObject().offsetX) == 0 
                            && parseInt(object.shadow.toObject().offsetY) == 0
                            && object.shadow.toObject().color == "#FFFFFF") {
                            object.set({
                                shadow: null
                            });
                        }
                    }
                });
            });
		},
        exportOption: function() {
            pdc.showLog("Active Export Options Events", "info");
            $('[pdc-data="export-option"]').click(function() {
                if($(this).hasClass("active")) return;
                var _optionName = $(this).find("input").attr("name"),
                    _optionValue = $(this).find("input").attr("value");
                switch(_optionName) {
                    case 'include_background':
                        PDCExport.toggleIncludeBackground(_optionValue);
                        break;
                    case 'include_overlay':
                        PDCExport.toggleIncludeOverlay(_optionValue);
                        break;    
                    case 'edit_design':
                        PDCExport.toggleEditDesign(_optionValue);
                        break;
                }
            });
        }(),
        toggleIncludeBackground: function(isInclude) {
            var self = this;
            var _canvas = self.getActiveCanvas();
            if(isInclude === "1") {
                pdc.showLog("Include background request", "info");
                if(self.canvasOriginalBackground.background_color) {
                    _canvas.setBackgroundColor(self.canvasOriginalBackground.background_color, _canvas.renderAll.bind(_canvas));    
                }
                if(self.canvasOriginalBackground.background_image) {
                    _canvas.setBackgroundImage(self.canvasOriginalBackground.background_image, _canvas.renderAll.bind(_canvas));    
                }
            } else {
                pdc.showLog("Exclude background request", "info");
                self.canvasOriginalBackground.background_color = _canvas.backgroundColor;
                self.canvasOriginalBackground.background_image = _canvas.backgroundImage;
                _canvas.setBackgroundImage(null, _canvas.renderAll.bind(_canvas));
                _canvas.setBackgroundColor(null, _canvas.renderAll.bind(_canvas));
            }
        },
        toggleIncludeOverlay: function(isInclude) {
            var self = this;
            var _canvas = self.getActiveCanvas();
            if(isInclude === "1") {
                pdc.showLog("Include overlay request", "info");
                if(self.canvasOriginalOverlay) {
                    _canvas.setOverlayImage(self.canvasOriginalOverlay, _canvas.renderAll.bind(_canvas));
                }
            } else {
                pdc.showLog("Exclude overlay request", "info");
                self.canvasOriginalOverlay = _canvas.overlayImage;
                _canvas.setOverlayImage(null, _canvas.renderAll.bind(_canvas));
                //_canvas.setOverlayColor(null, _canvas.renderAll.bind(_canvas));
            }
        },
        toggleEditDesign: function(isEditable) {
            var self = this;
             var _canvas = self.getActiveCanvas();
            if(isEditable === "1") {
                pdc.showLog("Edit design request", "info");
                _canvas.forEachObject(function(object){
                    object.set({
                        selectable: true
                    });
                });
            } else {
                _canvas.forEachObject(function(object){
                    object.set({
                        selectable: false
                    });
                });
                _canvas.deactivateAll().renderAll();
            }
        },
        getActiveCanvas: function() {
            var _activeCanvasId = "canvas_" + $("#canvas_list li.active a").attr("aria-controls");
            return allCanvas[_activeCanvasId];
        },
        downloadPng: function() {
            var self = this;
            pdc.renderThumbnail(pdc.setCurrentCanvas(self.getActiveCanvas()).canvas.toJSON(), {
                format: 'png', 
                multiplier: 1,
                url: baseUrl + 'pdp/index/saveBase64ImageExport/',
                order_info: self.getOrderInfo()
            }, 
            function(response) {
                pdc.showLog("The png file response to download png event", "info");
                var responseJson = JSON.parse(response);
                if(responseJson.status === "success") {
                    window.location = responseJson.thumbnail_path;
                    return false;
                }
                alert(responseJson.message);
            });
        },
        downloadSVG: function() {
            var self = this,
                canvasSvg = pdc.setCurrentCanvas(self.getActiveCanvas()).canvas.toSVG(),
                saveSvgUrl = baseUrl + 'pdp/index/saveAndCreateSvg';
            pdc.doRequest(saveSvgUrl, {
                svg_string: canvasSvg,
                order_info: self.getOrderInfo()
            }, function(response) {
                pdc.showLog("The svg file response to download svg event", "info");
                var responseJson = JSON.parse(response);
                if(responseJson.status === "success") {
                    window.location = responseJson.thumbnail_path;
                    return false;
                }
                alert(responseJson.message);
            });
        },
        downloadPdf: function() {
            var self = this,
                canvasSvg = pdc.setCurrentCanvas(self.getActiveCanvas()).canvas.toSVG(),
                saveSvgUrl = baseUrl + 'pdp/index/createPdfFromSvg';
            pdc.doRequest(saveSvgUrl, {
                svg_string: canvasSvg,
                order_info: self.getOrderInfo()
            }, function(response) {
                pdc.showLog("The pdf file response to download pdf event", "info");
                var responseJson = JSON.parse(response);
                if(responseJson.status === "success") {
                    window.location = responseJson.pdf_url;
                    return false;
                }
                alert(responseJson.message);
            });
        },
        downloadPdfFromPng: function() {
            var self = this,
                canvas = pdc.setCurrentCanvas(self.getActiveCanvas()).canvas,
                canvasBase64Code = canvas.toDataURL(),
                saveUrl = baseUrl + 'pdp/index/createPdfFromPng';
            pdc.doRequest(saveUrl, {
                png_string: canvasBase64Code,
                order_info: self.getOrderInfo()
            }, 
            function(response) {
                pdc.showLog("The pdf file response to download pdf event", "info");
                var responseJson = JSON.parse(response);
                if(responseJson.status === "success") {
                    window.location = responseJson.pdf_url;
                    return false;
                }
                alert(responseJson.message);
            });
        },
        getOrderInfo: function() {
            return {
                'order_id': $("#order_id").val(),
                'item_id': $("#item_id").val(),
                'increment_id': $("#increment_id").val(),
                'product_id': $("#product_id").val(),
                'side_label': $("#canvas_list li.active a").text(),
                'json_filename': $("#json_filename").val()
            }
        },
        exportBtnClickHandle: function() {
            var self = this;
            $('[pdc-data="pdc-export-btn"]').click(function() {
                var _action = $(this).attr("pdc-action");
                switch(_action) {
                    case 'DOWNLOAD_PDF_SVG' :
                        self.downloadPdf();
                        break;
                    case 'DOWNLOAD_PDF_PNG' :
                        self.downloadPdfFromPng();
                        break;    
                    case 'DOWNLOAD_SVG' :
                        self.downloadSVG();
                        break;
                    case 'DOWNLOAD_PNG' :
                        self.downloadPng();
                        break;   
                }   
            });
        }
	}
	PDCExport.init();
    PDCExport.exportBtnClickHandle();
});