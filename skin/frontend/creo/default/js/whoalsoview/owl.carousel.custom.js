jQuery(document).ready(function() {

      var owl = jQuery("#owl-demo");

      owl.owlCarousel({

      items : 4, //10 items above 1000px browser width
   //   itemsDesktop : [1000,4], //5 items between 1000px and 901px
     // itemsDesktopSmall : [900,4], // 3 items betweem 900px and 601px
      itemsTablet: [600,4], //2 items between 600 and 0;*/
      pagination : false,
      rewindNav : false,
      navigation : true,
      navigationText : ["prev","next"],
      itemsMobile : false // itemsMobile disabled - inherit from itemsTablet option*/
      
      
      });
      // Custom Navigation Events
      jQuery(".next").click(function(){
        owl.trigger('owl.next');
      })
      jQuery(".prev").click(function(){
        owl.trigger('owl.prev');
      })
      jQuery(".play").click(function(){
        owl.trigger('owl.play',1000);
      })
      jQuery(".stop").click(function(){
        owl.trigger('owl.stop');
      })
});
jQuery(window).load(function(){
 var max = 1;
 jQuery(".owl-carousel .owl-item").each(function(){
  var i = jQuery(this).index(i);
  var height1 = jQuery(this).height(); 
  max = height1 > max ? height1 : max;
 })
 jQuery(".owl-carousel .owl-item").css("height",max); 
})