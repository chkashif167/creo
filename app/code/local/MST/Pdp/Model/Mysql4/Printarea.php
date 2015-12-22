<?php

/**
* Magento Support Team.
* @category   MST
* @package    MST_Pdp
* @version    2.0
* @author     Magebay Developer Team <info@magebay.com>
* @copyright  Copyright (c) 2009-2013 MAGEBAY.COM. (http://www.magebay.com)
*/
class MST_Pdp_Model_Mysql4_Printarea extends Mage_Core_Model_Mysql4_Abstract
{

    public function _construct()
    {
        $this->_init('pdp/printarea', 'id');
    }
	public function getDesignPrintareaCollection(){
	
		$tbl_faq_item = $this->getTable('pdp/images');//Lay ten bang t2 de ket noi
		$table = $this->getMainTable();
		$collection = Mage :: getModel('pdp/printarea')->getCollection(); 
		$collection->getSelect()
			  ->join(array('t2' => $tbl_faq_item),'main_table.image_id = t2.image_id','t2.filename');//t2.name=> lay cac truong tu bang duoc JOIN
		/* $collection->setOrder('name','ASC');
		$collection->setOrder('depot_name','ASC');
		$collection->setOrder('suburb','ASC'); */
		return $collection;
	}
}