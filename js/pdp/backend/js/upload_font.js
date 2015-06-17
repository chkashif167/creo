var mst = jQuery.noConflict();
mst(document).ready(function($) {
	var formAction = $('#demoFiler').attr('action'),
	    skinUrl = $('#skin_url').val(),
        jsUrl = skinUrl.replace('skin/', 'js/pdp/backend/images/'),
        fontPath = $("#font_path").val(),
        imagePath = $("#image_path").val(),
	    uploadedFiles = new Array();
	//Update in php file too
	var config = {
			support : "ttf,otf,fnt,fon,woff,dfont",		//Upload font only, not image any more
			font : "ttf,otf,fnt,fon,woff,dfont",
			form: "demoFiler",					// Form ID
			dragArea: "dragAndDropFiles",		// Upload Area ID
			uploadUrl: formAction			// Server side upload url
		}
	function multiUploader(config){
		this.config = config;
		this.items = "";
		this.all = []
		var self = this;
		multiUploader.prototype._init = function(){
			if (window.File && 
				window.FileReader && 
				window.FileList && 
				window.Blob) {		
				 var inputId = $("#"+this.config.form).find("input[type='file']").eq(0).attr("id");
				 document.getElementById(inputId).addEventListener("change", this._read, false);
				 document.getElementById(this.config.dragArea).addEventListener("dragover", function(e){ e.stopPropagation(); e.preventDefault(); }, false);
				 document.getElementById(this.config.dragArea).addEventListener("drop", this._dropFiles, false);
				 document.getElementById("upload_font").addEventListener("click", this._submit, false);
			} else
				console.log("Browser supports failed");
		}
		multiUploader.prototype._submit = function(e){
			e.stopPropagation(); 
            e.preventDefault();
            if(self._fontValidate) {
                self._startUpload();
            }
		}
		multiUploader.prototype._preview = function(data){
			this.items = data;
			if(this.items.length > 0){
				var html = "";		
				var uId = "";
	 			for(var i = 0; i<this.items.length; i++){
					uId = this.items[i].name._unique();
					var sampleIcon = '<img src="'+ jsUrl +'image.png" />';
					var errorClass = "";
					
					if(typeof this.items[i] != undefined){
                        //console.log(this.items[i]);
						if(self._validate(this.items[i].type) <= 0 && self._fontValidate(this.items[i].name) == false) {
							sampleIcon = '<img src="'+ jsUrl +'unknown.png" />';
							errorClass =" invalid";
						} 
						html += '<div class="dfiles'+errorClass+'" rel="'+uId+'"><h5>'+sampleIcon+this.items[i].name+'</h5><div id="'+uId+'" class="progress" style="display:none;"><img src="' + jsUrl + 'ajax-loader.gif" /></div></div>';
					}
				}
				$("#dragAndDropFiles").append(html);
			}
		}
		multiUploader.prototype._read = function(evt){
			var arr = self.config.support.split(",");
			if(evt.target.files){
				self._preview(evt.target.files);
				//Turn off multi upload artwork
				if ($.inArray(evt.target.files[0].type.trim(), arr) !== -1) {
					//Reset image array
					self.all = [];
					self.all.push(evt.target.files);
				} else {
					self.all.push(evt.target.files);
				}
			} else 
				console.log("Failed file reading");
		}
		
		multiUploader.prototype._validate = function(format){
			var arr = this.config.support.split(",");
			return arr.indexOf(format);
		}
		/** If file is font type, pass validate**/
		multiUploader.prototype._fontValidate = function(fileName){
			var arr = this.config.font.split(",");
			var ext = fileName.split('.')[1];
			var pos = arr.indexOf(ext.toLowerCase());
			if (pos != -1) {
				return true;
			}
			return false;
		}
		multiUploader.prototype._dropFiles = function(e){
			e.stopPropagation(); e.preventDefault();
			self._preview(e.dataTransfer.files);
			self.all.push(e.dataTransfer.files);
		}
		multiUploader.prototype._uploader = function(file,f){
			if(typeof file[f] != undefined 
				&& (self._validate(file[f].type) > 0 || self._fontValidate(file[f].name) == true)){
				var data = new FormData();
				var ids = file[f].name._unique();
				var upload_file_type = $('#upload_file_type').val();
				data.append('file',file[f]);
				data.append('index',ids);
				data.append('base_dir', $('#base_dir').val());
				data.append('file_type', file[f].type);
                data.append('upload_file_type', upload_file_type);
				data.append('image_type', $('#image_type').val());
				data.append('filename', file[f].name);
				$(".dfiles[rel='"+ids+"']").find(".progress").show();
				//If file had updated, then dont send request
				if ($.inArray(file[f].name._unique(), uploadedFiles) == -1) {
					$.ajax({
						type: "POST",
						url: this.config.uploadUrl,
						data:data,
						cache: false,
						contentType: false,
						processData: false,
						beforeSend : function() {
							$("#loading-mask").attr("style","left: -2px; top: 0px; width: 1034px; height: 833px;").show();
						},
						success: function(rponse){
							$("#"+ids).hide();
							var obj = $(".dfiles").get();
                            var dataInfo = $.parseJSON(rponse);
                            var row = "";
							$.each(obj,function(k,fle){
								if($(fle).attr("rel") == dataInfo.index){
									$(fle).slideUp("normal", function(){ $(this).remove(); });
								}
							});
							if (f+1 < file.length) {
								self._uploader(file,f+1);
							} else{
                                location.reload();
							}
							//Push file had updated to updated array
							uploadedFiles.push(file[f].name._unique());
							$("#loading-mask").hide();
						}
					});
				} else {
					//console.log(file[f].name + ' have just updated');
					//If some file had updated, then remove div from drop area
					var obj = $(".dfiles").get();
					$.each(obj,function(k,fle){
						if($.inArray($(fle).attr("rel"), uploadedFiles) != -1){
							$(fle).slideUp("normal", function(){ $(this).remove(); });
						}
					});
				}

			} else {
				alert("Invalid file format - " + file[f].name);
				location.reload();
			}
			//console.log(uploadedFiles);
		}
		
		multiUploader.prototype._startUpload = function(){
			if(this.all.length > 0){
				for(var k=0; k<this.all.length; k++){
					var file = this.all[k];
					this._uploader(file,0);
				}
			}
		}
		
		String.prototype._unique = function(){
			return this.replace(/[a-zA-Z]/g, function(c){
	     	   return String.fromCharCode((c <= "Z" ? 90 : 122) >= (c = c.charCodeAt(0) + 13) ? c : c - 26);
	    	});
		}

		this._init();
	}

	function initMultiUploader(){
		new multiUploader(config);
	}
	initMultiUploader(config);
});