	/*
	*
	*
	*/
	

jQuery.noConflict();
jQuery(function($) {
	var myhref,qsbtt;

	// base function
	
	//get IE version
	function ieVersion(){
		var rv = -1; // Return value assumes failure.
		if (navigator.appName == 'Microsoft Internet Explorer'){
			var ua = navigator.userAgent;
			var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
			if (re.exec(ua) != null)
				rv = parseFloat( RegExp.$1 );
		}
		return rv;
	}

	//read href attr in a tag
	function readHref(){
		var mypath = arguments[0];
		var patt = /\/[^\/]{0,}$/ig;
		if(mypath[mypath.length-1]=="/"){
			mypath = mypath.substring(0,mypath.length-1);
			return (mypath.match(patt)+"/");
		}
		return mypath.match(patt);
	}


	//string trim
	function strTrim(){
		return arguments[0].replace(/^\s+|\s+$/g,"");
	}

	function _qsJnit(){
	

		
		var selectorObj = arguments[0];
			//selector chon tat ca cac li chua san pham tren luoi
		var listprod = $(selectorObj.itemClass);
		var qsImg;
		var mypath = 'quickshop/index/view';
		if(EM.QuickShop.BASE_URL.indexOf('index.php') == -1){
			mypath = 'index.php/quickshop/index/view';
		}
		var baseUrl = EM.QuickShop.BASE_URL + mypath;
		
		var _qsHref = "<a id=\"em_quickshop_handler\" href=\"#\" style=\"visibility:hidden;position:absolute;top:0;left:0\"><img  alt=\"quickshop\" src=\""+EM.QuickShop.QS_IMG+"\" /></a>";
		$(document.body).append(_qsHref);
		
		var qsHandlerImg = $('#em_quickshop_handler img');

		$.each(listprod, function(index, value) { 
			var reloadurl = baseUrl;
			
			//get reload url
			myhref = $(value).children(selectorObj.aClass );
			var prodHref = readHref(myhref.attr('href'))[0];
			prodHref[0] == "\/" ? prodHref = prodHref.substring(1,prodHref.length) : prodHref;
			prodHref=strTrim(prodHref);
			
			reloadurl = baseUrl+"/path/"+prodHref;	
			version = ieVersion();	
			if(version < 8.0 && version > -1){
				reloadurl = baseUrl+"/path"+prodHref;
			}
			//end reload url

			
			$(selectorObj.imgClass, this).bind('mouseover', function() {
				var o = $(this).offset();
				$('#em_quickshop_handler').attr('href',reloadurl).show()
					.css({
						'top': o.top+($(this).height() - qsHandlerImg.height())/2+'px',
						'left': o.left+($(this).width() - qsHandlerImg.width())/2+'px',
						'visibility': 'visible'
					});
			});
			$(value).bind('mouseout', function() {
				$('#em_quickshop_handler').hide();
			});
		});

		//fix bug image disapper when hover
		$('#em_quickshop_handler')
			.bind('mouseover', function() {
				$(this).show();
			})
			.bind('click', function() {
				$(this).hide();
			});
		//insert quickshop popup
		
		$('#em_quickshop_handler').fancybox({
				'width'				: EM.QuickShop.QS_FRM_WIDTH,
				'height'			: EM.QuickShop.QS_FRM_HEIGHT,
				'autoScale'			: false,
				'padding'			: 0,
				'margin'			: 0,
				//'transitionIn'		: 'none',
				//'transitionOut'		: 'none',
				'type'				: 'iframe',
				onComplete: function() { 
					$.fancybox.showActivity();
					$('#fancybox-frame').unbind('load');
					$('#fancybox-frame').bind('load', function() {
						$.fancybox.hideActivity();
					});
				}
		});


	
	
	}

	//end base function


	_qsJnit({
		itemClass : '.products-grid li.item', //selector for each items in catalog product list,use to insert quickshop image
		aClass : 'a.product-image', //selector for each a tag in product items,give us href for one product
		imgClass: '.product-image img' //class for quickshop href
	});



});


