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
        maxWidth	: 900,
        maxHeight	: 700,
        fitToView	: false,
        width		: '80%',
        height		: '75%',
        autoSize	: false,
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
 });
