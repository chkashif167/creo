var insta = jQuery.noConflict();
	
insta(document).ready(function($){
    G = 
{
	loadSpeed	: 800,
	slideWidth 	: 600,
	moveSelectedBack	: function()
	{
		var $div = $('.selected').prev();
			$('div').removeClass('selected');
			$div.addClass('selected');
		G.checkNavigation();
	},
	moveSelectedAhead	: function()
	{
		var $el = $('.selected').next();
			$('div').removeClass('selected');
			$el.addClass('selected');
		G.checkNavigation();
	},
	checkNavigation		: function()
	{
		// Check if we are on the first page
		if ($('.selected').prev().length == 0)
		{
			$('.prev').attr('disabled', 'true');
		}
		else 
		{
			$('.prev').removeAttr('disabled');
		}
		// Check if we are on the last page
		if ($('.selected').next().length == 0)
		{
			$('.next').attr('disabled', 'true');
		}
		else 
		{
			$('.next').removeAttr('disabled');
		}
	},
	loadNextPage		: function(data, isInit)
	{			
		if (isInit)
		{
			G.moveSelectedAhead();
		}
		// Grab the url for the next page of images
		var url = data.pagination.next_url;	
		// Create new div page
		var $div = '';//$("<div>");
		$.each(data.data, function(key, value){		
			// Create the image element 
			var $img = '<img src="'+value.images.standard_resolution.url+'" full="'+value.images.standard_resolution.url+'" color="" />';
			//$div.append($img);value.images.thumbnail.url
            $div += $img;
		});		
		// Add new page to the slider
		$('#pdc_ins_myphotos').append($div);
		// Change the width of the images div since we are 
		// adding another page
		var width = $('.image-content .images').width();
		$('.image-content .images').width(width + G.slideWidth);
		// Check to see if we are on the last page
		if (typeof url === 'undefined')
		{
			//$('#pdc_ins_myphotos').addClass('end');	
            $('.pdp_ins_next').hide();
		}
		else
		{
			$('.pdp_ins_next').data('nexturl', url);					
		}
	},
	requestNextPage 	: function(url, isInit)
	{	
		if (isInit)
		{
			$('.pdp_ins_next').attr('disabled', 'true');
		}
		$.ajax({
			type: "GET",
			url: $('#url_site').val().replace('index.php/','')+"js/pdp/instagram/next.php",
			data: { url: url },
			dataType: 'json',
			success: function(data)
			{
				G.loadNextPage(data, isInit);
				if (isInit)
				{
					$('.pdp_ins_next').removeAttr('disabled');
				}
                $('#pdc_ins_myphotos').removeClass('loading');
				G.checkNavigation();
			}
		});
	},
	// For the main page
	updateInstagramImage 	: function(full, thumb)
    {
        $('.instagram-img').attr('src', full);
    },
    updateAllInstagramImage : function(html,url,id,token){
        $('.instagram-signin').hide();
        //$('.content_instagram').prepend('<a class="pdp_ins_your_photos active pdp_ins_tab active">Your Photos</a><a class="pdp_ins_tab pdp_ins_your_friend_photos">Friends Photos</a>')
        $('#pdc_ins_myphotos').html(html);
        if(url!=undefined){
            $('.pdp_ins_next').attr('data-nexturl',url).show(); 
       }else{
            $('.pdp_ins_next').attr('data-nexturl','').hide(); 
       }
        if($('#pdc_user_id').length == 0){
           $('#pdc_instagram_list_img').append('<input type="hidden" id="pdc_user_id" value="'+id+'" /><input type="hidden" id="pdc_user_access_token" value="'+token+'" />'); 
        }
        $('.instagram-signout, #pdc_instagram_list_img').show();
    },
    ajax_follows : function(url,follows){
        $.ajax({
    		type: "GET",
    		dataType: "jsonp",
    		cache: true,
            url: url,
            beforeSend : function() {
				//$('#pdc_ins_feed_friends .loading-img').show();
			},
    		success: function(data) {
    		 for(i=0;i<data.data.length;i++){
                follows.push(data.data[i]);
             }
    		 if(data.pagination.next_url!=undefined){
    		      G.ajax_follows(data.pagination.next_url,follows);
    		 }else{
    		      //console.log(follows);
                  var url = "https://api.instagram.com/v1/users/"+$('#pdc_user_id').val()+"/followed-by/?access_token="+$('#pdc_user_access_token').val(),
                  followed_by = [];
                  G.ajax_followed_by(url,follows,followed_by);
    		 }
    		}
    	});
    },
    ajax_followed_by : function(url,follows,followed_by){
        $.ajax({
    		type: "GET",
    		dataType: "jsonp",
    		cache: true,
            url: url,
            beforeSend : function() {
				$('[pdc-ins="ins_friends_block"] .loading-img').show();
			},
    		success: function(data) {
    		 for(i=0;i<data.data.length;i++){
                followed_by.push(data.data[i]);
             }
    		 if(data.pagination.next_url!=undefined){
    		      G.ajax_followed_by(data.pagination.next_url,follows,followed_by);
    		 }else{
    		      //console.log(follows);
                  //console.log(followed_by);
                  $('[pdc-ins="ins_friends_block"]').addClass('loaded');
                  $('[pdc-ins="ins_friends_block"] .loading-img').hide();
                  G.find_friend_instagram(follows,followed_by);
    		 }
    		}
    	});
    },
    find_friend_instagram: function(follows,followed_by){
        var friends = [];
        for(i=0;i<follows.length;i++){
            var id = follows[i].id;
            for(j=0;j<followed_by.length;j++){
                if(followed_by[j].id==id){
                    $('[pdc-ins="ins_friends_block"] ul').append('<div ins_id="'+follows[i].id+'" class="pdp_ins_friend_list"><img src="'+follows[i].profile_picture+'"/><label>'+follows[i].username+'</label></div>');
                    break;
                }
            }
         }
    },
    show_ins_images: function(id){
        var url = "https://api.instagram.com/v1/users/"+id+"/media/recent/?access_token="+$('#pdc_user_access_token').val()
        $('#pdp_ins_feed_friends_image_list').show();
        $.ajax({
    		type: "GET",
    		dataType: "jsonp",
    		cache: true,
            url: url,
            beforeSend : function() {
				$('#pdp_ins_feed_friends .loading-img').show();
			},
    		success: function(data) {
    		  $('#pdp_ins_feed_friends .loading-img').hide();
    		 for(i=0;i<data.data.length;i++){
    		   $('#pdp_ins_feed_friends_image_list').prepend('<img src="'+data.data[i].images.standard_resolution.url+'" user="'+id+'" color="" />')
                //follows.push(data.data[i]);
             }
    		 if(data.pagination.next_url!=undefined){
    		      $('.pdp_ins_friends_images_next').show();
                  if($('#pdp_ins_load_more_id_'+id).length==0){
                    $('#pdp_ins_feed_friends_image_list').append('<input type="hidden" value="'+data.pagination.next_url+'" id="pdp_ins_load_more_id_'+id+'" />');
                  }                  
    		      //G.ajax_follows(data.pagination.next_url,follows);
    		 }else{
    		      $('.pdp_ins_friends_images_next').hide();
                  $('#pdp_ins_load_more_id_'+id).val('');
    		 }
    		}
    	});
    },
    show_ins_images_next: function(url,id){
        $('#pdp_ins_feed_friends_image_list').show();
        $.ajax({
    		type: "GET",
    		dataType: "jsonp",
    		cache: true,
            url: url,
            beforeSend : function() {
				$('#pdp_ins_feed_friends .loading-img').show();
			},
    		success: function(data) {
    		  $('#pdp_ins_feed_friends .loading-img').hide();
    		 for(i=0;i<data.data.length;i++){
    		   $('#pdp_ins_feed_friends_image_list').append('<img src="'+data.data[i].images.standard_resolution.url+'" user="'+id+'" color="" />')
                //follows.push(data.data[i]);
             }
    		 if(data.pagination.next_url!=undefined){
    		      $('.pdp_ins_friends_images_next').show();
                  $('#pdp_ins_load_more_id_'+id).val(data.pagination.next_url);   
    		 }else{
    		      $('.pdp_ins_friends_images_next').hide();
                  $('#pdp_ins_load_more_id_'+id).val('');
    		 }
    		}
    	});
    }
};
	/** MAIN PAGE CODE **/
	$('.instagram-signin').on('click', function(){
        var redirect_url    = $('#pdc_ins_redirect_url').val();
        var client_id       = $('#pdc_ins_api').val();
        var url = 'https://api.instagram.com/oauth/authorize/?client_id=' + client_id + '&redirect_uri=' + redirect_url + '&response_type=code';
        window.open(url, "Instagram", "menubar=1,resizable=1,width=600,height=600");
    });
    $('.instagram-signout').click(function(){
        $('#pdc_ins_myphotos, #pdc_ins_friendsphotos').html('');
        $('.pdp_ins_next').attr('data-nexturl','');
        //$('.pdp_ins_next, .pdp_ins_tab, .instagram-signout, #pdp_search_ins_friend').remove();
        $('.instagram-signin').show();
        $(this).hide();
        $('#pdc_instagram_list_img').hide();
        var s = document.createElement("script");
        s.src = "https://instagram.com/accounts/logout";
        $("head").append(s);
    });
    $('#pdc_ins_action li').click(function(){
        if(!$(this).hasClass('active')){
            $('#pdc_ins_action li.active').removeClass('active');
            $(this).addClass('active');
            var rel = $(this).attr('rel');
            $('[pdc-ins]').hide();
            $('[pdc-ins][rel="'+rel+'"]').show();
        }
    })
    /** FILE CHOOSER POP-UP CODE **/
	// Preload one slide
	var url = $('.next').data('nexturl');
	//G.requestNextPage(url, false);				
	// Live click listener for the thumbnails
	$('.images img').on('click', function(){
		var $this = $(this);
		// This function exists in the parent window
		window.opener.G.updateInstagramImage($this.data('full'), $this.data('thumb'));
		window.close();
	});	
    //
    $('.content_instagram').on('click', '.pdp_ins_friend_list', function(){
        $('.pdp_ins_friend_list.active').removeClass('active');
        $(this).addClass('active');
        $('#pdp_ins_feed_friends > ul, #pdp_search_ins_friend').hide();
        $('.pdp_back_ins_cate').show();
            var id = $(this).attr('ins_id');
        $('#pdp_ins_feed_friends_list, #pdp_ins_feed_friends_image_list img').hide();
        if(!$(this).hasClass('loaded')){
            $(this).addClass('loaded');
            G.show_ins_images(id);
        }else{
            if($('#pdp_ins_load_more_id_'+id).val()!=''){
                $('.pdp_ins_friends_images_next').show();
            }else{
                $('.pdp_ins_friends_images_next').hide();
            }
            $('#pdp_ins_feed_friends_image_list, #pdp_ins_feed_friends_image_list img[user='+id+']').show();
        }
    });
    $('.content_instagram').on('keyup', '#pdp_search_ins_friend', function(){
        var key = $(this).val().toUpperCase(); 
       $('#pdp_ins_feed_friends_list div').each(function(){
            if($(this).children('label').text().toUpperCase().indexOf(key)>=0){
                $(this).show();
            }else{
                $(this).hide();
            }
       });
    });
    $('.content_instagram').on('click', '.pdp_back_ins_cate', function(){
        $('#pdp_ins_feed_friends_image_list, .pdp_back_ins_cate, .pdp_ins_friends_images_next').hide();
        $('#pdp_ins_feed_friends_list, #pdp_ins_feed_friends_list div').show();
        $('#pdp_search_ins_friend').val('').show();
    });
    $('.content_instagram').on('click', '.pdp_ins_friends_images_next', function(){
        var id = $('#pdp_ins_feed_friends_list .active').attr('ins_id'),
        url = $('#pdp_ins_load_more_id_'+id).val();
        G.show_ins_images_next(url,id);
    });
    $('.content_instagram').on('click', '.pdp_ins_tab', function(){
        if($(this).hasClass('active')){return false;}else{
            $('.content_instagram .pdp_ins_tab.active').removeClass('active');
            $(this).addClass('active');
            if($(this).hasClass('pdp_ins_your_photos')){
                $('#pdp_ins_feed, .pdp_ins_next').show();
                $('#pdp_ins_feed_friends').hide();
            }else{
                $('#pdp_ins_feed, .pdp_ins_next').hide();
                $('#pdp_ins_feed_friends').show();
                if(!$(this).hasClass('loaded')){
                    $(this).addClass('loaded');
                    var follows = followed_by = [], 
                    url = "https://api.instagram.com/v1/users/"+$('#pdc_user_id').val()+"/follows/?access_token="+$('#pdc_user_access_token').val();
            		G.ajax_follows(url,follows);
                }
            }
        }
	});
		
	// Show the next page
	$('.content_instagram').on('click', '.pdp_ins_next', function(){
		// Check to see if we are on the last slide
		if ((!$('#pdc_ins_myphotos').hasClass('end'))&&(!$('#pdc_ins_myphotos').hasClass('loading')))
		{
		    $('#pdc_ins_myphotos').addClass('loading');
			// Move the ahead a page
			//$('.image-content .images').animate({left: '-=' + G.slideWidth}, G.loadSpeed);
            
			// We haven't loaded all of the instagram pages 
			// so we need to request the next ones
			//if (!$('.images div').hasClass('end'))
			//{
				// Request the next slide
				var url = $(this).data('nexturl');
				G.requestNextPage(url, true);										
			//}
			//else
		//	{
				// Move the selected class ahead to the next div
		//		G.moveSelectedAhead();
		//	}
		}
		else 
		{
			G.checkNavigation();
		}
	});	
    $('#pdc_ins_myphotos').scroll(function(){
        var top = $(this).find('img:last-child').offset().top; 
        //console.log(parseInt($(this).offset().top));
        if((parseInt(top)<=625)&&(!$(this).hasClass('end'))){
            //$('.pdp_ins_next').click();
        }
    });
    $('#pdp_ins_feed_friends_image_list').scroll(function(){
        var top = $(this).find('img:last-child').offset().top; 
        //console.log(top);
    });
	// Show previous page
	$('.prev').on('click', function(){
		var $img_content = $('.image-content .images');
		var left = $img_content.position().left;
		if (left != 0)
		{
			$img_content.animate({left: '+=600'}, G.loadSpeed);
			G.moveSelectedBack();
		}
	});		
});
