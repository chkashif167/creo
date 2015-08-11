<?php
class MST_PDP_Block_Adminhtml_Template_Grid_Renderer_Shape extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action {
    public function render(Varien_Object $row)
    {
        return $this->_getValue($row);
    }
	public function _getValue(Varien_Object $row)
    {
        if ($getter = $this->getColumn()->getGetter()) {
            $val = $row->$getter();
        }
        $val = $row->getData($this->getColumn()->getIndex());
        $val = str_replace("no_selection", "", $val);
        $url =  Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'pdp/shapes/' . $val;

        $out = '<center><a href="'.$url.'" target="_blank" id="imageurl">';
        $out .= "<img src=". $url ." width='60px' ";
        $out .=" />";
        $out .= '</a></center>';

        return $out;

    }
}