<?php
class Mango_Attributeswatches_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/attributeswatches?id=15 
    	 *  or
    	 * http://site.com/attributeswatches/id/15 	
    	 */
    	/* 
		$attributeswatches_id = $this->getRequest()->getParam('id');

  		if($attributeswatches_id != null && $attributeswatches_id != '')	{
			$attributeswatches = Mage::getModel('attributeswatches/attributeswatches')->load($attributeswatches_id)->getData();
		} else {
			$attributeswatches = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($attributeswatches == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$attributeswatchesTable = $resource->getTableName('attributeswatches');
			
			$select = $read->select()
			   ->from($attributeswatchesTable,array('attributeswatches_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$attributeswatches = $read->fetchRow($select);
		}
		Mage::register('attributeswatches', $attributeswatches);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}