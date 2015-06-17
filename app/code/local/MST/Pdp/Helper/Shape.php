<?php
class MST_Pdp_Helper_Shape extends Mage_Core_Helper_Abstract {
    public function pagingCollection($current_page, $page_size, $view_per_page, $collection, $total, $url, $category){
		
		$collection_counter = $total;
		$collection->setCurPage($current_page);
	    $collection->setPageSize($page_size);
		
		$skin_url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN);
		$arrowLeft = $skin_url . "adminhtml/default/default/images/pager_arrow_left.gif" ;
		$arrowRight = $skin_url . "adminhtml/default/default/images/pager_arrow_right.gif" ;
		$paging_text="<div class='paging-area'>";
       	# Get total pages
		$size = ceil($collection_counter/$page_size);
		$paging_text .= "Page ";
		# Previous button
		if($current_page != 1 ){
       		$page = $current_page - 1;
       		$paging_text.="<div id='previous_div'><a id='previous_page_btn' href='#' onclick='ShapeItem.pagingCollection(this.id,\"".$url."\")'>".'<img class="arrow" alt="Go to Previous page" src="'. $arrowLeft .'">'."</a></div>";	
		}else{
			$paging_text .= '<img class="arrow" alt="Go to Previous page" src="'. $arrowLeft .'">';
		}
		# Input textbox enter page number
		$paging_text .= "<input class='span1' type='text' id='current_page_input' name='current_page_input' size='1' value='". $current_page ."'/>";
		# Next button
   		if($current_page != (int)$size){
       		$page = $current_page + 1;
       		$paging_text .= "<div id='next_div'><a id='next_page_btn' href='#' onclick='ShapeItem.pagingCollection(this.id,\"". $url ."\")'>".'<img class="arrow" alt="Go to Next page" src="'.$arrowRight.'">'."</a></div>";	
		}else{
			$paging_text .= '<img class="arrow" alt="Go to Next page" src="' . $arrowRight . '">';
		}
		# View per page dropdown
		$view_dropdown = "<select id='view_per_page' class='span1' name='view_per_page' onchange='ShapeItem.pagingCollection(this.id,\"". $url ."\")'>";
		foreach($view_per_page as $option){
			$view_dropdown .= "<option value='". $option ."' ".(($option == $page_size)? 'selected="selected"' : '').">$option</option>";
		}
		$view_dropdown .= "</select>";
		
		# Category
		$categorys = $this->getCategoryFilterOptions();
		$category_dropdown = "<select id='category_filter' name='category_filter' onchange='ShapeItem.pagingCollection(this.id,\"". $url ."\")'>";
		foreach($categorys as $key => $value){
			$category_dropdown .= "<option value='". $key ."' ".(((string)$key === $category)? 'selected="selected"' : '').">$value</option>";
		}
		$category_dropdown .= "</select>";
		
		$paging_text .= " of ". $size ." pages | View $view_dropdown | Category $category_dropdown | Total ". $collection_counter ." records found."; 
		$paging_text.="</div>";//End paging-are div
		return array(
			'paging_text'=> $paging_text,
			'collection' => $collection,
		);
	}
    public function getCategoryFilterOptions()
	{
		$category = Mage::getModel('pdp/shapecate')->getCategoryFilterOptions();
		return $category;
	}
}