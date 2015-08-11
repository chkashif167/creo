<?php
class MST_PDP_Block_Adminhtml_Template_Grid_Renderer_Color extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action {
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

        $out = '<center><span style=\'background:#'.$val.'; display: table; padding: 0px; height: 40px; width: 100px; position: relative; border: 0px solid rgb(255, 255, 255);border-radius:100% 0;\'><span style=\'position: absolute; width: 100%; background: rgba(0, 0, 0, 0.2); bottom: 0px; height: 5px;border-radius:100% 0;\'></span></span></center>';

        return $out;

    }
}