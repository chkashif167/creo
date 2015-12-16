// -----------------------------------------------------------------------------------
//
//	Lightbox v2.05
//	by Lokesh Dhakar - http://www.lokeshdhakar.com
//	Last Modification: 3/18/11
//
//	For more information, visit:
//	http://lokeshdhakar.com/projects/lightbox2/
//
//	Licensed under the Creative Commons Attribution 2.5 License - http://creativecommons.org/licenses/by/2.5/
//  	- Free for use in both personal and commercial projects
//		- Attribution requires leaving author name, author link, and the license info intact.
//	
//      Thanks: Scott Upton(uptonic.com), Peter-Paul Koch(quirksmode.com), and Thomas Fuchs(mir.aculo.us) for ideas, libs, and snippets.
//  		Artemy Tregubenko (arty.name) for cleanup and help in updating to latest ver of proto-aculous.
//
//
//      Customized for Mageworx needs, 2012
// -----------------------------------------------------------------------------------
/*

    Table of Contents
    -----------------
    Configuration

    Lightbox Class Declaration
    - initialize()
    - updateImageList()
    - start()
    - changeImage()
    - resizeImageContainer()
    - showImage()
    - updateDetails()
    - updateNav()
    - enableKeyboardNav()
    - disableKeyboardNav()
    - keyboardAction()
    - preloadNeighborImages()
    - end()
    
    Function Calls
    - document.observe()
   
*/
// -----------------------------------------------------------------------------------

//
//  Configurationl
//
MageworxLightboxOptions = Object.extend({
    fileLoadingImage:        '/js/mageworx/lightbox/images/loading.gif',     
    fileBottomNavCloseImage: '/js/mageworx/lightbox/images/closelabel.gif',

    overlayOpacity: 0.8,   // controls transparency of shadow overlay

    animate: true,         // toggles resizing animations
    resizeSpeed: 7,        // controls the speed of the image resizing animations (1=slowest and 10=fastest)

    borderSize: 10,         //if you adjust the padding in the CSS, you will need to update this variable

	// When grouping images this is used to write: Image # of #.
	// Change it for non-english localization
	labelImage: "Image",
	labelOf: "of"
}, window.MageworxLightboxOptions || {});

// -----------------------------------------------------------------------------------
var MageworxLightbox = Class.create();

MageworxLightbox.prototype = {
    imageArray: [],
    activeImage: undefined,
    
    // initialize()
    // Constructor runs on completion of the DOM loading. Calls updateImageList and then
    // the function inserts html at the bottom of the page which is used to display the shadow 
    // overlay and the image container.
    //
    initialize: function() {    
        
        this.updateImageList();
        
        this.keyboardAction = this.keyboardAction.bindAsEventListener(this);

        if (MageworxLightboxOptions.resizeSpeed > 10) MageworxLightboxOptions.resizeSpeed = 10;
        if (MageworxLightboxOptions.resizeSpeed < 1)  MageworxLightboxOptions.resizeSpeed = 1;

	    this.resizeDuration = MageworxLightboxOptions.animate ? ((11 - MageworxLightboxOptions.resizeSpeed) * 0.15) : 0;
	    this.overlayDuration = MageworxLightboxOptions.animate ? 0.2 : 0;  // shadow fade in/out duration

        // When Lightbox starts it will resize itself from 250 by 250 to the current image dimension.
        // If animations are turned off, it will be hidden as to prevent a flicker of a
        // white 250 by 250 box.
        var size = (MageworxLightboxOptions.animate ? 250 : 1) + 'px';
        

        // Code inserts html at the bottom of the page that looks similar to this:
        //
        //  <div id="overlay"></div>
        //  <div id="lightbox">
        //      <div id="outerImageContainer">
        //          <div id="imageContainer">
        //              <img id="lightboxImage">
        //              <div style="" id="hoverNav">
        //                  <a href="#" id="prevLink"></a>
        //                  <a href="#" id="nextLink"></a>
        //              </div>
        //              <div id="loading">
        //                  <a href="#" id="loadingLink">
        //                      <img src="images/loading.gif">
        //                  </a>
        //              </div>
        //          </div>
        //      </div>
        //      <div id="imageDataContainer">
        //          <div id="imageData">
        //              <div id="imageDetails">
        //                  <span id="caption"></span>
        //                  <span id="numberDisplay"></span>
        //              </div>
        //              <div id="bottomNav">
        //                  <a href="#" id="bottomNavClose">
        //                      <img src="images/close.gif">
        //                  </a>
        //              </div>
        //          </div>
        //      </div>
        //  </div>


        var objBody = $$('body')[0];

	objBody.appendChild(Builder.node('div',{id:'mageworxOverlay'}));
	
        objBody.appendChild(Builder.node('div',{id:'mageworxLightbox'}, [
            Builder.node('div',{id:'mageworxOuterImageContainer'}, 
                Builder.node('div',{id:'mageworxImageContainer'}, [
                    Builder.node('img',{id:'mageworxLightboxImage'}), 
                    Builder.node('div',{id:'mageworxHoverNav'}, [
                        Builder.node('a',{id:'mageworxPrevLink', href: '#' }),
                        Builder.node('a',{id:'mageworxNextLink', href: '#' })
                    ]),
                    Builder.node('div',{id:'mageworxLoading'}, 
                        Builder.node('a',{id:'mageworxLoadingLink', href: '#' }, 
                            Builder.node('img', {src: MageworxLightboxOptions.fileLoadingImage})
                        )
                    )
                ])
            ),
            Builder.node('div', {id:'mageworxImageDataContainer'},
                Builder.node('div',{id:'mageworxImageData'}, [
                    Builder.node('div',{id:'mageworxImageDetails'}, [
                        Builder.node('span',{id:'mageworxCaption'}),
                        Builder.node('span',{id:'mageworxNumberDisplay'})
                    ]),
                    Builder.node('div',{id:'mageworxBottomNav'},
                        Builder.node('a',{id:'mageworxBottomNavClose', href: '#' },
                            Builder.node('img', { src: MageworxLightboxOptions.fileBottomNavCloseImage })
                        )
                    )
                ])
            )
        ]));


        $('mageworxOverlay').hide().observe('click', (function() { this.end(); }).bind(this));
        $('mageworxLightbox').hide().observe('click', (function(event) { if (event.element().id == 'mageworxLightbox') this.end(); }).bind(this));
        $('mageworxOuterImageContainer').setStyle({ width: size, height: size });
        $('mageworxPrevLink').observe('click', (function(event) { event.stop(); this.changeImage(this.activeImage - 1); }).bindAsEventListener(this));
        $('mageworxNextLink').observe('click', (function(event) { event.stop(); this.changeImage(this.activeImage + 1); }).bindAsEventListener(this));
        $('mageworxLoadingLink').observe('click', (function(event) { event.stop(); this.end(); }).bind(this));
        $('mageworxBottomNavClose').observe('click', (function(event) { event.stop(); this.end(); }).bind(this));

        var th = this;
        (function(){
            var ids = 
                'mageworxOverlay mageworxLightbox mageworxOuterImageContainer mageworxImageContainer mageworxLightboxImage mageworxHoverNav mageworxPrevLink mageworxNextLink mageworxLoading mageworxLoadingLink ' + 
                'mageworxImageDataContainer mageworxImageData mageworxImageDetails mageworxCaption mageworxNumberDisplay mageworxBottomNav mageworxBottomNavClose';   
            $w(ids).each(function(id){ th[id] = $(id); });
        }).defer();
    },

    //
    // updateImageList()
    // Loops through anchor tags looking for 'lightbox' references and applies onclick
    // events to appropriate links. You can rerun after dynamically adding images w/ajax.
    //
    updateImageList: function() {   
        this.updateImageList = Prototype.emptyFunction;
        document.observe('click', (function(event){
            var target = event.findElement('a[rel^=mageworxLightbox]');
            if (target && target.href) {
                event.stop();
                this.start(target);
            }
        }).bind(this));
    },
    
    //
    //  start()
    //  Display overlay and lightbox. If image is part of a set, add siblings to imageArray.
    //
    start: function(imageLink) {
        
        if ($(imageLink).hasClassName('disableLightbox')) {
            $(imageLink).removeClassName('disableLightbox');
            return false;
        }
        
        $$('select', 'object', 'embed').each(function(node){ node.style.visibility = 'hidden' });

        // stretch overlay to fill page and fade in
        var arrayPageSize = this.getPageSize();
        $('mageworxOverlay').setStyle({ width: arrayPageSize[0] + 'px', height: arrayPageSize[1] + 'px' });

        new Effect.Appear(this.mageworxOverlay, { duration: this.overlayDuration, from: 0.0, to: MageworxLightboxOptions.overlayOpacity });

        this.imageArray = [];
        var imageNum = 0;       

        if ((imageLink.getAttribute("rel") == 'mageworxLightbox')){
            // if image is NOT part of a set, add single image to imageArray
            this.imageArray.push([imageLink.href, imageLink.title]);         
        } else {
            // if image is part of a set..
            this.imageArray = 
                $$(imageLink.tagName + '[href][rel="' + imageLink.rel + '"]').
                collect(function(anchor){ return [anchor.href, anchor.title]; }).
                uniq();
            
            while (this.imageArray[imageNum][0] != imageLink.href) { imageNum++; }
        }

        // calculate top and left offset for the lightbox 
        var arrayPageScroll = document.viewport.getScrollOffsets();
        var lightboxTop = arrayPageScroll[1] + (document.viewport.getHeight() / 10);
        var lightboxLeft = arrayPageScroll[0];
        this.mageworxLightbox.setStyle({ top: lightboxTop + 'px', left: lightboxLeft + 'px' }).show();
        
        this.changeImage(imageNum);
    },

    //
    //  changeImage()
    //  Hide most elements and preload image in preparation for resizing image container.
    //
    changeImage: function(imageNum) {   
        
        this.activeImage = imageNum; // update global var

        // hide elements during transition
        if (MageworxLightboxOptions.animate) this.mageworxLoading.show();
        this.mageworxLightboxImage.hide();
        this.mageworxHoverNav.hide();
        this.mageworxPrevLink.hide();
        this.mageworxNextLink.hide();
		// HACK: Opera9 does not currently support scriptaculous opacity and appear fx
        this.mageworxImageDataContainer.setStyle({opacity: .0001});
        this.mageworxNumberDisplay.hide();      
        
        var imgPreloader = new Image();
        
        // once image is preloaded, resize image container
        imgPreloader.onload = (function(){
            this.mageworxLightboxImage.src = this.imageArray[this.activeImage][0];
            /*Bug Fixed by Andy Scott*/
            this.mageworxLightboxImage.width = imgPreloader.width;
            this.mageworxLightboxImage.height = imgPreloader.height;
            /*End of Bug Fix*/
            this.resizeImageContainer(imgPreloader.width, imgPreloader.height);
        }).bind(this);
        imgPreloader.src = this.imageArray[this.activeImage][0];
    },

    //
    //  resizeImageContainer()
    //
    resizeImageContainer: function(imgWidth, imgHeight) {

        // get current width and height
        var widthCurrent  = this.mageworxOuterImageContainer.getWidth();
        var heightCurrent = this.mageworxOuterImageContainer.getHeight();

        // get new width and height
        var widthNew  = (imgWidth  + MageworxLightboxOptions.borderSize * 2);
        var heightNew = (imgHeight + MageworxLightboxOptions.borderSize * 2);

        // scalars based on change from old to new
        var xScale = (widthNew  / widthCurrent)  * 100;
        var yScale = (heightNew / heightCurrent) * 100;

        // calculate size difference between new and old image, and resize if necessary
        var wDiff = widthCurrent - widthNew;
        var hDiff = heightCurrent - heightNew;

        if (hDiff != 0) new Effect.Scale(this.mageworxOuterImageContainer, yScale, {scaleX: false, duration: this.resizeDuration, queue: 'front'}); 
        if (wDiff != 0) new Effect.Scale(this.mageworxOuterImageContainer, xScale, {scaleY: false, duration: this.resizeDuration, delay: this.resizeDuration}); 

        // if new and old image are same size and no scaling transition is necessary, 
        // do a quick pause to prevent image flicker.
        var timeout = 0;
        if ((hDiff == 0) && (wDiff == 0)){
            timeout = 100;
            if (Prototype.Browser.IE) timeout = 250;   
        }

        (function(){
            this.mageworxPrevLink.setStyle({ height: imgHeight + 'px' });
            this.mageworxNextLink.setStyle({ height: imgHeight + 'px' });
            this.mageworxImageDataContainer.setStyle({ width: widthNew + 'px' });

            this.showImage();
        }).bind(this).delay(timeout / 1000);
    },
    
    //
    //  showImage()
    //  Display image and begin preloading neighbors.
    //
    showImage: function(){
        this.mageworxLoading.hide();
        new Effect.Appear(this.mageworxLightboxImage, { 
            duration: this.resizeDuration, 
            queue: 'end', 
            afterFinish: (function(){ this.updateDetails(); }).bind(this) 
        });
        this.preloadNeighborImages();
    },

    //
    //  updateDetails()
    //  Display caption, image number, and bottom nav.
    //
    updateDetails: function() {
    
        this.mageworxCaption.update(this.imageArray[this.activeImage][1]).show();

        // if image is part of set display 'Image x of x' 
        if (this.imageArray.length > 1){
            this.mageworxNumberDisplay.update( MageworxLightboxOptions.labelImage + ' ' + (this.activeImage + 1) + ' ' + MageworxLightboxOptions.labelOf + '  ' + this.imageArray.length).show();
        }

        new Effect.Parallel(
            [ 
                new Effect.SlideDown(this.mageworxImageDataContainer, { sync: true, duration: this.resizeDuration, from: 0.0, to: 1.0 }), 
                new Effect.Appear(this.mageworxImageDataContainer, { sync: true, duration: this.resizeDuration }) 
            ], 
            { 
                duration: this.resizeDuration, 
                afterFinish: (function() {
	                // update overlay size and update nav
	                var arrayPageSize = this.getPageSize();
	                this.mageworxOverlay.setStyle({ width: arrayPageSize[0] + 'px', height: arrayPageSize[1] + 'px' });
	                this.updateNav();
                }).bind(this)
            } 
        );
    },

    //
    //  updateNav()
    //  Display appropriate previous and next hover navigation.
    //
    updateNav: function() {

        this.mageworxHoverNav.show();               

        // if not first image in set, display prev image button
        if (this.activeImage > 0) this.mageworxPrevLink.show();

        // if not last image in set, display next image button
        if (this.activeImage < (this.imageArray.length - 1)) this.mageworxNextLink.show();
        
        this.enableKeyboardNav();
    },

    //
    //  enableKeyboardNav()
    //
    enableKeyboardNav: function() {
        document.observe('keydown', this.keyboardAction); 
    },

    //
    //  disableKeyboardNav()
    //
    disableKeyboardNav: function() {
        document.stopObserving('keydown', this.keyboardAction); 
    },

    //
    //  keyboardAction()
    //
    keyboardAction: function(event) {
        var keycode = event.keyCode;

        var escapeKey;
        if (event.DOM_VK_ESCAPE) {  // mozilla
            escapeKey = event.DOM_VK_ESCAPE;
        } else { // ie
            escapeKey = 27;
        }

        var key = String.fromCharCode(keycode).toLowerCase();
        
        if (key.match(/x|o|c/) || (keycode == escapeKey)){ // close lightbox
            this.end();
        } else if ((key == 'p') || (keycode == 37)){ // display previous image
            if (this.activeImage != 0){
                this.disableKeyboardNav();
                this.changeImage(this.activeImage - 1);
            }
        } else if ((key == 'n') || (keycode == 39)){ // display next image
            if (this.activeImage != (this.imageArray.length - 1)){
                this.disableKeyboardNav();
                this.changeImage(this.activeImage + 1);
            }
        }
    },

    //
    //  preloadNeighborImages()
    //  Preload previous and next images.
    //
    preloadNeighborImages: function(){
        var preloadNextImage, preloadPrevImage;
        if (this.imageArray.length > this.activeImage + 1){
            preloadNextImage = new Image();
            preloadNextImage.src = this.imageArray[this.activeImage + 1][0];
        }
        if (this.activeImage > 0){
            preloadPrevImage = new Image();
            preloadPrevImage.src = this.imageArray[this.activeImage - 1][0];
        }
    
    },

    //
    //  end()
    //
    end: function() {
        this.disableKeyboardNav();
        this.mageworxLightbox.hide();
        new Effect.Fade(this.mageworxOverlay, { duration: this.overlayDuration });
        $$('select', 'object', 'embed').each(function(node){
            if (node.style.height!='1px') node.style.visibility = 'visible';
        });
    },

    //
    //  getPageSize()
    //
    getPageSize: function() {
	        
	     var xScroll, yScroll;
		
		if (window.innerHeight && window.scrollMaxY) {	
			xScroll = window.innerWidth + window.scrollMaxX;
			yScroll = window.innerHeight + window.scrollMaxY;
		} else if (document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac
			xScroll = document.body.scrollWidth;
			yScroll = document.body.scrollHeight;
		} else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
			xScroll = document.body.offsetWidth;
			yScroll = document.body.offsetHeight;
		}
		
		var windowWidth, windowHeight;
		
		if (self.innerHeight) {	// all except Explorer
			if(document.documentElement.clientWidth){
				windowWidth = document.documentElement.clientWidth; 
			} else {
				windowWidth = self.innerWidth;
			}
			windowHeight = self.innerHeight;
		} else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
			windowWidth = document.documentElement.clientWidth;
			windowHeight = document.documentElement.clientHeight;
		} else if (document.body) { // other Explorers
			windowWidth = document.body.clientWidth;
			windowHeight = document.body.clientHeight;
		}	

		// for small pages with total height less then height of the viewport
		if(yScroll < windowHeight){
			pageHeight = windowHeight;
		} else { 
			pageHeight = yScroll;
		}
	
		// for small pages with total width less then width of the viewport
		if(xScroll < windowWidth){	
			pageWidth = xScroll;		
		} else {
			pageWidth = windowWidth;
		}

		return [pageWidth,pageHeight];
	}
}

document.observe('dom:loaded', function () {new MageworxLightbox(); });