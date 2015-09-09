<?php
class VES_PdfPro_Block_Adminhtml_Key_Grid_Renderer_Store extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Store
{
    public function render(Varien_Object $row)
    {
    	$value	= $row->getData($this->getColumn()->getIndex());
    	$row->setData($this->getColumn()->getIndex(),explode(',', $value));
    	return parent::render($row);
    }
    
}
