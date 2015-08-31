<?php

class Mango_Attributeswatches_Block_Adminhtml_Attributeswatches_Grid_Render_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $value = $row->getData($this->getColumn()->getIndex());
        return '<span style="border:solid 1px #000;background-image:url('. Mage::getBaseUrl('media') . 'attributeswatches/' . $value .');display:inline-block;float:left;width:30px;height:15px;"></span>&nbsp;' . $value;
    }

}

?>