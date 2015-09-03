/**
 * Featured Product 
 * 
 * @category    design
 * @package     base_default
 * @author      Clarion Magento Team <magento@clariontechnologies.co.in>
 */
// Avoid PrototypeJS conflicts, assign jQuery to $j instead of $
var $j = jQuery.noConflict();
function showFeaturedProduct(pageno, urlController){
    
    $j(document).ready(function(){
        $j("#loading").show();
        //For layout dependent Columns
        var columnCount = $j('#columnCount').val();
        var featuredPrdoctsOnPage = $j('#featuredPrdoctsOnPage').val();
        $j.get(urlController, {p:pageno, cols:columnCount, pagename:featuredPrdoctsOnPage},
        function(data,status){
           if(status == 'success') {
              // alert("Data: " + data + "\nStatus: " + status);
               $j("#featuredproductlist").html(data);
               $j("#loading").hide();
           }
        }
        ,"html");
    });
}

function showLeftSidebarFeaturedProduct(pageno, urlController){
    $j(document).ready(function(){
         $j("#left-sidebar-loading").show();
        $j.get(urlController, {p:pageno},
        function(data,status){
           if(status == 'success') {
              // alert("Data: " + data + "\nStatus: " + status);
               $j("#featured-product-left-sidebar").html(data);
               $j("#left-sidebar-loading").hide();
           }
        }
        ,"html");
    });
}

function showRightSidebarFeaturedProduct(pageno, urlController){
    $j(document).ready(function(){
         $j("#right-sidebar-loading").show();
        $j.get(urlController, {p:pageno},
        function(data,status){
           if(status == 'success') {
              // alert("Data: " + data + "\nStatus: " + status);
               $j("#featured-product-right-sidebar").html(data);
               $j("#right-sidebar-loading").hide();
           }
        }
        ,"html");
    });
}