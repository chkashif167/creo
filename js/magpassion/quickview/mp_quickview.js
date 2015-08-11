/**
 * MagPassion_Quickview extension
 * 
 * @category   	MagPassion
 * @package		MagPassion_Quickview
 * @copyright  	Copyright (c) 2014 by MagPassion (http://magpassion.com)
 * @license	http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


jQuery.noConflict();
jQuery(document).ready(function(){
    jQuery(".mp_quickview_icon").fancybox({
        maxWidth	: 630,
        maxHeight	: 700,
        fitToView	: false,
        autoSize	: true,
        closeClick	: false,
        openEffect	: 'none',
        closeEffect	: 'none'
    });
    //
    jQuery("a.mpquickviewproductmoreimg").click(function( e ) {
        e.preventDefault();
        imgid = jQuery(this).attr('id');
        imgid = imgid.substring(0, imgid.indexOf("_"));
        jQuery('#'+imgid).attr('src', jQuery(this).attr('href'));
        return false;
      });
		
		
		 jQuery(".fancybox-iframe").fancybox({
            'titleShow'  : false,
            'autoscale' : true,
            'width'  : '630',
            'height'  : '800',
            'transitionIn'  : 'elastic',
            'transitionOut' : 'elastic',
				'type'        : 'iframe',
				'scrolling'   : 'no',
				 'overflow' : 'hidden', 
				'iframe': {'scrolling': 'no'}
            }); 
        
		
 });
