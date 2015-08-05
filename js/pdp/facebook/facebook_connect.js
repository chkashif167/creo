var magebay_facebook = jQuery.noConflict();
magebay_facebook(document).ready(function($){
    var scope = 'user_photos',
        album_html = $('[pdc-block="fb_albums"]').html();
    Mb_Fb = {
        check_status: function(response){
            if(response.status=="connected"){
                Mb_Fb.get_infos();
            }else{
                $('#magebay_fb_button .not_login').show();
                $('#magebay_fb_button .login').hide();
                $('#magebay_fb_button').removeClass('logged');
                Mb_Fb.loggin_fb();
            }
        },
        checkLoginState: function() {
            FB.getLoginStatus(function(response) {
              Mb_Fb.check_status(response);
            });
        },
        loggin_fb: function(){
            FB.login(function(response) {
                // handle the response
                if(response.status=='connected'){
                    $('#magebay_fb_button .not_login').hide();
                    $('#magebay_fb_button .login, #user-info, #photos_album').show();
                    $('#magebay_fb_button').addClass('logged');
                    Mb_Fb.get_infos(response);
                }
            }, {
                scope: scope,
                return_scopes: true
            });  
        },
        logout_fb: function(){
            FB.logout(function(response) {
                $('#magebay_fb_button .not_login').show();
                $('#magebay_fb_button .login, #user-info, #photos_album').hide();
                $('#magebay_fb_button').removeClass('logged');
                $('[pdc-fb-info="avatar"], [pdc-block="fb_albums"], [pdc-fb-info="name"], [pdc-fb-info="list_img"]').html('');
                // Person is now logged out
            });  
        },
        get_infos: function(rep){
            FB.api('/me/permissions', function(response) {
                console.log(response);
            });
            FB.api('/me?fields=id,name,picture,albums', function(response) {
                $('[pdc-fb-info="name"]').html(response.name);
                $('[pdc-fb-info="avatar"]').html('<img src="'+response.picture.data.url+'" />');
                if(response.albums!=undefined){
                    var l=response.albums.data.length,
                        rs_all = album_html;
                    if(l > 0){
                        for (var i=0; i<l; i++){
                            var album = response.albums.data[i],
                                albumid = album.id;
                                rs_all += '<option value="'+album.id+'">'+album.name+'</option>';
                        }
                        $('[pdc-block="fb_albums"]').html(rs_all);
                    }
                }else{
                    $('[pdc-block="fb_albums"]').html(album_html);
                }
                
            });
        },
        get_all_photos : function(id){
            var photos_album = $('[pdc-fb-info="list_img"]');
            if(id==0){
                photos_album.html('');
                return;
            }
            FB.api("/"+id+"/photos",function(response){
                var photos = response["data"],
                    pt_result = '<ul>';
                for(var pt=0;pt<photos.length;pt++) {
                    //console.log(photos[pt].images[0].source);
                    pt_result += '<li><img color="" src="'+photos[pt].images[0].source+'" /></li>';
                }
                pt_result   +=  '</ul>';
                photos_album.html(pt_result);
            });
        },
        init: function(){
            $('[pdc-block="fb_albums"]').on('change',function(){
                Mb_Fb.get_all_photos($(this).val());
            });
            $('#magebay_fb_button').click(function(){
                if(!$(this).hasClass('logged')){
                    Mb_Fb.loggin_fb();
                }else{
                    Mb_Fb.logout_fb();
                }
            });
        }()
  
    }
})