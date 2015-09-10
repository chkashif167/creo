<?php
class VES_PdfPro_Block_Adminhtml_Key_Grid_Renderer_Group extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
    	$value	= $row->getData($this->getColumn()->getIndex());
    	$groups = Mage::getModel('customer/group')->getCollection()->addFieldToFilter('customer_group_id',array('in'=>explode(',', $value)));
    	$result = '';
    	foreach($groups as $group){$result.=$group->getCustomerGroupCode()."<br />";}
    	return $result;
    }
    
}
